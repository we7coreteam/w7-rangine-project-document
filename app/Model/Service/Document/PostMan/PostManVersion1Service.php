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

use W7\App\Model\Entity\Document;

class PostManVersion1Service extends PostManCommonService
{
	public function buildJson($documentId)
	{
		$data = [
			'variables' => [],
			'info' => $this->getDocumentInfo($documentId),
			'item' => $this->getDocumentItem($documentId),
		];
		return $data;
	}

	public function getDocumentItem($documentId)
	{
		$item = $this->getDocumentItemChildren($documentId, 0);
		return $item;
	}

	public function getDocumentItemChildren($documentId, $parentId)
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
						'item' => $this->getDocumentItemChildren($documentId, $val->id)
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
		$chapter = [
			'name' => $chapter->name,
			'request' => [
				'url' => 'http=>//127.0.0.1=>99/test/index2',
				'method' => 'POST',
				'header' => [
					[
						'key' => 'Content-Type',
						'value' => 'application/x-www-form-urlencoded',
						'description' => ''
					]
				],
				'body' => [
					'mode' => 'urlencoded',
					'urlencoded' => [
						[
							'key' => 'a',
							'value' => '3',
							'type' => 'text',
							'enabled' => true
						]
					]
				],
				'description' => ''
			],
			'response' => []
		];
		return $chapter;
	}

	public function getDocumentInfo($documentId)
	{
		$document = Document::query()->find($documentId);
		if ($document) {
			$info = [
				'name' => $document->name,
				'_postman_id' => 'document-' . $document->id,
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
