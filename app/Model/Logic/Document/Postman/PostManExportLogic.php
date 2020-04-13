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

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Logic\Document\ChapterApi\ChapterDemoLogic;
use W7\App\Model\Logic\Document\ChapterApiLogic;
use W7\App\Model\Service\AES;

class PostManExportLogic extends PostManCommonLogic
{
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
		$chapterApiLogic = new ChapterApiLogic();
		$methodLabel = $chapterApiLogic->getMethodLabel();
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
				$chapterDemoLogic = new ChapterDemoLogic($chapterApi->chapter_id);
				$demo = $chapterDemoLogic->getChapterDemo(0, 1, [ChapterApiParam::LOCATION_REPONSE_BODY_RAW]);
				$body = json_encode($demo['data']);
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
		$chapterDemoLogic = new ChapterDemoLogic($chapterId);
		$demo = $chapterDemoLogic->getChapterDemo(0, 3, $locationList);
		$data = $demo['data'];
		$descriptionData = $demo['descriptionData'];
		$reply = [];
		foreach ($data as $key => $val) {
			$row = [
				'key' => $key,
				'value' => $val,
				'type' => 'text'
			];
			if (isset($descriptionData[$key]) && $descriptionData[$key]) {
				$row['description'] = $descriptionData[$key];
			}
			$reply[] = $row;
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
