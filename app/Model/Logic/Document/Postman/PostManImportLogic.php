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

namespace W7\App\Model\Logic\Document\PostMan;

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Logic\Document\ChapterApi\ChapterChangeLogic;
use W7\App\Model\Logic\Document\ChapterApi\ChapterRecordLogic;
use W7\App\Model\Logic\Document\ChapterApiLogic;

class PostManImportLogic extends PostManCommonLogic
{
	protected $descriptionData = [];

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
//			throw new ErrorHttpException('导入未开启！');
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
			$this->importRequest($documentId, $request, $chapter, $data);
		}

		return $chapter;
	}

	public function getKeyValueDataToArray($info1)
	{
		//键值对数组
		$reply = [];
		$descriptionData = [];
		foreach ($info1 as $key => $val) {
			if (isset($val['key'])) {
				$name = $val['key'];
				$value = '';
				$description = '';
				if (isset($val['value'])) {
					$value = $val['value'];
				}
				if (isset($val['description'])) {
					$description = $val['description'];
				}
				$reply[$key] = urlencode($name) . '=' . urlencode($value);
				$descriptionData[$key] = urlencode($name) . '=' . urlencode($description);
			}
		}
		//http参数
		$newStr = implode('&', $reply);
		parse_str($newStr, $result);

		//http参数描述
		$newStr2 = implode('&', $descriptionData);
		parse_str($newStr2, $result2);

		$this->descriptionData = $result2;
		return $result;
	}

	public function changeFormat($data, $dataType = 0)
	{
		if ($dataType == 0) {
			//默认POSTMAN专用-键值对数组
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
			$obj = new ChapterChangeLogic();
			$infoData = $obj->arrayToData($info, $this->descriptionData);
			//补齐描述
			return $infoData;
		}
		return false;
	}

	public function importRequest($documentId, $request, $chapter, $data)
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

			if (isset($data['response'][0]['body']) && $data['response'][0]['body']) {
				$body[ChapterApiParam::LOCATION_REPONSE_BODY_RAW] = $this->changeFormat($data['response'][0]['body'], 1);
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

				$obj = new ChapterRecordLogic($chapter->id);
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
		$chapterApiLogic = new ChapterApiLogic();
		$methodList = $chapterApiLogic->getMethodLabel();
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
}
