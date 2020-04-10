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

namespace W7\App\Model\Service\Document;

use W7\App\Model\Entity\Document;

class PostManService
{
	protected $documentId;
	protected $version;

	public function __construct($documentId, $version = 2)
	{
		$this->documentId = $documentId;
		$this->version = $version;
	}

	public function documentToPostMan()
	{
		if ($this->version == 2) {
			$data = $this->buildJsonV2();
		} else {
			$data = $this->buildJsonV1();
		}
		return $data;

//		Chapter::query()->where('document_id', $documentId)->where('parent_id', 0)->get();
	}

	public function buildJsonV1()
	{
		return [];
	}

	public function buildJsonV2()
	{
		$documentId = $this->documentId;
		$data = [
			'variables' => [],
			'info' => $this->getDocumentInfoV2($documentId),
			'item' => $this->getDocumentItemV2($documentId),
		];
		return $data;
	}

	public function getDocumentItemV2($documentId)
	{
		$item = $this->getDocumentItemV2Children($documentId, 0);
		return $item;
	}

	public function getDocumentItemV2Children($documentId, $parentId)
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
						'item' => $this->getDocumentItemV2Children($documentId, $val->id)
					];
					$item[$key] = $row;
				} else {
					//章节是http类型
					if ($val->content && $val->content->layout == 1) {
						$item[$key] = $this->getChapterInfoV2($val);
					}
				}
			}
		}
		return $item;
	}

	public function getChapterApi()
	{
	}

	public function getChapterInfoV2($chapter)
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

	public function getDocumentInfoV2($documentId)
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

	public function postManToDocument()
	{
	}
}
