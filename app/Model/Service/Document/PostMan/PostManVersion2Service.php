<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Model\Service\Document\PostMan;

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Service\AES;
use W7\App\Model\Service\Document\ChapterChangeService;
use W7\App\Model\Service\Document\ChapterDemoService;
use W7\App\Model\Service\Document\ChapterRecordService;

class PostManVersion2Service extends PostManCommonService
{
	//POSTMENJSON导入目录
	public function importToDocument($userId, $json)
	{
		if ($this->isJson($json)) {
			$data = json_decode($json, true);
			if (isset($data['info']['schema'])) {
				if (in_array($data['info']['schema'], ['https://schema.getpostman.com/json/collection/v2.0.0/collection.json', 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'])) {
					//版本2或者2.1
					if (isset($data['item']) && $data['item'] && is_array($data['item'])) {
						//数据完整
						return $this->importData($userId, $data['info'], $data['item']);
					}
					throw new ErrorHttpException('导入数据为空！');
				}
				throw new ErrorHttpException('导入失败：仅支持POSTMAN Collection V2或V2.1版本数据导入！');
			} else {
				if (isset($data['id']) && isset($data['requests'])) {
					//可能是V1版本
					throw new ErrorHttpException('仅支持POSTMAN Collection V2版本的数据格式导入！请升级您的POSTMAN');
				}
			}
		}
		throw new ErrorHttpException('导入失败：当前不是标准的POSTMAN Collection V2版本数据！');
	}

	public function importData($userId, $info, $item)
	{
		idb()->beginTransaction();
		try {
			$document = $this->importDocument($userId, $info);
			$reply = $this->importItem($document->id, $item, 0);
			idb()->commit();
			return $reply;
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function importItem($documentId, $item, $parentId)
	{
		foreach ($item as $key => $val) {
			if (isset($val['item']) && $val['item'] && is_array($val['item'])) {
				//如果是目录
				$this->importDir($documentId, $val, $parentId);
			} elseif (isset($val['request']) && $val['request']) {
				//如果是请求
				$this->importChapter($documentId, $val, $parentId);
			}
		}
		return true;
	}

	public function importChapter($documentId, $data, $parentId)
	{
		$name = '';
		if (isset($data['name'])) {
			$name = $data['name'];
		}
		$maxSort = Chapter::query()->where('document_id', '=', $documentId)->where('parent_id', '=', $parentId)->max('sort');
		$sort = ++$maxSort;
		//创建目录
		$chapter = Document\Chapter::query()->create([
			'parent_id' => $parentId,
			'name' => $name,
			'document_id' => $documentId,
			'sort' => $sort,
			'is_dir' => 0
		]);
		$request = $data['request'];
		if ($request && is_array($request)) {
			$this->importRequest($documentId, $request, $chapter);
		}
		return $chapter;
	}

	public function getKeyValueDataToArray($info1)
	{
		//键值对数组
		$reply = [];
		foreach ($info1 as $key => $val) {
			if (isset($val['key'])) {
				$name = $val['key'];
				$value = '';
				if (isset($val['value'])) {
					$value = $val['value'];
				}
				$reply[$key] = urlencode($name) . '=' . urlencode($value);
			}
		}
		//http参数
		$newStr = implode('&', $reply);
		parse_str($newStr, $result);
		return $result;
	}

	public function changeFormat($data, $dataType = 3)
	{
		if ($dataType == 3) {
			//键值对数组
			$info = $this->getKeyValueDataToArray($data);
		} elseif ($dataType == 1) {
			//json
			if ($this->isJson($data)) {
				$info = json_decode($data, true);
			}
		} else {
			//普通数组
			$info = $data;
		}
		if (is_array($info)) {
			//键值对数组转换为键值对文本
			$obj = new ChapterChangeService();
			$infoData = $obj->arrayToData($info);
			//补齐描述
			return $infoData;
		}
		return false;
	}

	public function importRequest($documentId, $request, $chapter)
	{
		//导入内容

		$url = '';
		$method = 'GET';
		$description = '';
		$body = [];
		$body_param_location = ChapterApiParam::LOCATION_REQUEST_BODY_FROM;
		if (isset($request['url'])) {
			if (is_array($request['url'])) {
				if (isset($request['url']['raw'])) {
					$url = $request['url']['raw'];
				}
				if (isset($request['url']['query']) && $request['url']['query'] && is_array($request['url']['query'])) {
					//get参数转换成标准格式
					$body[ChapterApiParam::LOCATION_REQUEST_QUERY] = $this->changeFormat($request['url']['query']);
				}
			} else {
				$url = $request['url'];
			}
			if (isset($request['description']) && $request['description']) {
				$description = $request['description'];
			}
			if (isset($request['method']) && $request['method']) {
				$method = $request['method'];
			}
			if (isset($request['header']) && $request['header'] && is_array($request['header'])) {
				$body[ChapterApiParam::LOCATION_REQUEST_HEADER] = $this->changeFormat($request['header']);
			}
			if (isset($request['body']) && is_array($request['body'])) {
				$postManBody = $request['body'];
				if (isset($postManBody['mode'])) {
					if ($postManBody['mode'] == 'formdata') {
						$body_param_location = ChapterApiParam::LOCATION_REQUEST_BODY_FROM;
						if (is_array($postManBody['formdata'])) {
							$body[ChapterApiParam::LOCATION_REQUEST_BODY_FROM] = $this->changeFormat($postManBody['formdata']);
						}
					} elseif ($postManBody['mode'] == 'urlencoded') {
						$body_param_location = ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED;
						if (isset($postManBody['urlencoded']) && is_array($postManBody['urlencoded'])) {
							$body[ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED] = $this->changeFormat($postManBody['urlencoded']);
						}
					} elseif ($postManBody['mode'] == 'raw') {
						$body_param_location = ChapterApiParam::LOCATION_REQUEST_BODY_RAW;
						if (isset($postManBody['raw']) && $postManBody['raw']) {
							$body[ChapterApiParam::LOCATION_REQUEST_BODY_RAW] = $this->changeFormat($postManBody['raw'], 1);
						}
					} elseif ($postManBody['mode'] == 'file') {
						$body_param_location = ChapterApiParam::LOCATION_REQUEST_BODY_BINARY;
					}
				}
			}

			if ($url) {
				$record = [
					'api' => [
						'url' => $url,
						'method' => $this->getMethodId($method),
						'description' => $description,
						'status_code' => 200,
						'body_param_location' => $body_param_location
					],
					'body' => $body
				];

				$obj = new ChapterRecordService($chapter->id);
				$content = $obj->recordToMarkdown($record);

				if (!empty($chapter->content)) {
					$chapter->content->content = $content;
					$chapter->content->save();
				} else {
					ChapterContent::query()->create([
						'chapter_id' => $chapter->id,
						'content' => $content,
						'layout' => ChapterContent::LAYOUT_HTTP
					]);
				}
				return true;
			}
		}
		return false;
	}

	public function getMethodId($method)
	{
		$methodList = ChapterApi::getMethodLabel();
		foreach ($methodList as $key => $val) {
			if ($val == $method) {
				return $key;
			}
		}
		return ChapterApi::METHOD_GET;
	}

	public function importDir($documentId, $data, $parentId)
	{
		$name = '';
		if (isset($data['name'])) {
			$name = $data['name'];
		}
		$maxSort = Chapter::query()->where('document_id', '=', $documentId)->where('parent_id', '=', $parentId)->max('sort');
		$sort = ++$maxSort;
		//创建目录
		$chapter = Document\Chapter::query()->create([
			'parent_id' => $parentId,
			'name' => $name,
			'document_id' => $documentId,
			'sort' => $sort,
			'is_dir' => 1
		]);
		$item = $data['item'];
		$this->importItem($documentId, $item, $chapter->id);
		return $chapter;
	}

	public function importDocument($userId, $info)
	{
		$name = '';
		$description = '';
		if (isset($info['name'])) {
			$name = $info['name'];
		}
		if (isset($info['description'])) {
			$description = $info['description'];
		}
		if (!$name) {
			$name = 'document-' . date('Y-m-d H:i:s');
		}
		$document = Document::query()->create([
			'name' => trim($name),
			'description' => $description,
			'creator_id' => $userId,
			'is_public' => 1,
		]);
		return $document;
	}

	//目录转POSTMENJSON
	public function buildExportJson($documentId)
	{
		$data = [
			'variables' => [],
			'info' => $this->getInfo($documentId),
			'item' => $this->getItemChildren($documentId, 0),
		];
		return $data;
	}

	public function getItemChildren($documentId, $parentId)
	{
		$item = [];
		$chapterList = Document\Chapter::query()->where('document_id', $documentId)->where('parent_id', $parentId)->get();
		if ($chapterList) {
			foreach ($chapterList as $key => $val) {
				if ($val->is_dir) {
					//是目录
					$row = [
						'name' => $val->name,
						'description' => '',
						'item' => $this->getItemChildren($documentId, $val->id)
					];
					$item[$key] = $row;
				} else {
					//章节是http类型
					if ($val->content && $val->content->layout == 1) {
						$item[$key] = $this->getChapterInfo($val);
					}
				}
			}
		}
		return $item;
	}

	public function getChapterInfo($chapter)
	{
		$url = '';
		$method = 1;
		$description = '';
		$chapterApi = Document\ChapterApi::query()->where('chapter_id', $chapter->id)->first();
		if ($chapterApi) {
			$url = $chapterApi->url;
			$method = $chapterApi->method;
			$description = $chapterApi->description;
		}
		$methodLabel = ChapterApi::getMethodLabel();
		$chapter = [
			'name' => $chapter->name,
			'request' => [
				'method' => $methodLabel[$method],
				'header' => $this->getFrom($chapterApi->chapter_id, [ChapterApiParam::LOCATION_REQUEST_HEADER]),
				'body' => $this->getBody($chapterApi),
				'url' => $this->getUrl($chapterApi),
				'description' => $description
			],
			'response' => [],
		];
		$chapter['response'] = $this->getResponse($chapterApi, $chapter['request']);
		return $chapter;
	}

	public function getResponse($chapterApi, $request)
	{
		$reply = [];
		$header = $this->getFrom($chapterApi->chapter_id, [ChapterApiParam::LOCATION_REPONSE_HEADER]);
		$form = $this->getFrom($chapterApi->chapter_id, [ChapterApiParam::LOCATION_REPONSE_BODY_RAW]);
		$body = '';
		if ($header || $form) {
			if ($form) {
				$chapterDemoService = new ChapterDemoService($chapterApi->chapter_id);
				$body = $chapterDemoService->getChapterDemo(0, 1, [ChapterApiParam::LOCATION_REPONSE_BODY_RAW]);
			}
			$response = [
				'name' => $chapterApi->url,
				'originalRequest' => [
					'method' => $request['method'],
					'header' => $request['header'],
					'body' => $request['body'],
					'url' => $request['url'],
				],
				'status' => 'OK',
				'code' => $chapterApi->status_code,
				'_postman_previewlanguage' => 'json',
				'header' => $header,
				'cookie' => [],
				'body' => $body
			];
			$reply[] = $response;
		}

		return $reply;
	}

	public function getBody($chapterApi)
	{
		$body_param_location = 3;
		if ($chapterApi->body_param_location) {
			if (in_array($chapterApi->body_param_location, array_keys($this->requestBodyIds()))) {
				$body_param_location = $chapterApi->body_param_location;
			}
		}

		$mode = 'formdata';
		$from = $this->getFrom($chapterApi->chapter_id, [$body_param_location]);
		if ($body_param_location == ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED) {
			$mode = 'urlencoded';
		} elseif ($body_param_location == ChapterApiParam::LOCATION_REQUEST_BODY_RAW) {
			$mode = 'raw';
			$from = json_encode($from);
		} elseif ($body_param_location == ChapterApiParam::LOCATION_REQUEST_BODY_BINARY) {
			$mode = 'file';
			$from = json_encode($from);
		}
		$body = [
			'mode' => $mode,
			$mode => $from
		];
		return $body;
	}

	public function getUrl($chapterApi)
	{
		$reply = [];
		if ($chapterApi) {
			$url = $chapterApi->url;
			$protocol = explode('://', $url);
			if (count($protocol) > 1) {
				$urlStr1 = str_replace($protocol[0] . '://', '', $url);
				$getData = explode('?', $urlStr1);
				$urlStr2 = $getData[0];
				$dirData = explode('/', $urlStr2);
				$path = str_replace($dirData[0], '', $urlStr2);
				$path = explode('/', $path);

				$port = explode(':', $dirData[0]);
				$host = explode('.', $port[0]);
				$reply = [
					'raw' => $url,
					'protocol' => $protocol[0],
					'host' => $host,
					'path' => $path,
					'query' => $this->getFrom($chapterApi->chapter_id, [ChapterApiParam::LOCATION_REQUEST_QUERY])
				];
				if (count($port) > 1 && $port[1]) {
					$reply['port'] = $port[1];
				}
			}
		}
		return $reply;
	}

	public function getFrom($chapterId, $locationList)
	{
		$chapterDemoService = new ChapterDemoService($chapterId);
		$data = $chapterDemoService->getChapterDemo(0, 3, $locationList);
		$reply = [];
		foreach ($data as $key => $val) {
			$reply[] = [
				'key' => $key,
				'value' => $val,
				'type' => 'text'
			];
		}
		return $reply;
	}

	public function getInfo($documentId)
	{
		$document = Document::query()->find($documentId);
		if ($document) {
			$obj = new AES();
			$info = [
				'name' => $document->name,
				'_postman_id' => 'document-' . $obj->encrypt($document->id),
				'description' => $document->description,
				'schema' => 'https://schema.getpostman.com/json/collection/v2.0.0/collection.json'
			];
		} else {
			$info = [
				'name' => '',
				'_postman_id' => 'abc-' . time() . '-' . rand(100, 999),
				'description' => '',
				'schema' => 'https://schema.getpostman.com/json/collection/v2.0.0/collection.json'
			];
		}
		return $info;
	}
}
