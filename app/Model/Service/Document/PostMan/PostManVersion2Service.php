<?php


namespace W7\App\Model\Service\Document\PostMan;


use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Service\Document\ChapterDemoService;

class PostManVersion2Service extends PostManCommonService
{
	public function buildJson($documentId)
	{
		$data = [
			'variables' => [],
			'info' => $this->getInfo($documentId),
			'item' => $this->getItemChildren($documentId,0),
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
		$url='';$method=1;$description='';
		$chapterApi=Document\ChapterApi::query()->where('chapter_id',$chapter->id)->first();
		if($chapterApi){
			$url=$chapterApi->url;
			$method=$chapterApi->method;
			$description=$chapterApi->description;
		}
		$methodLabel = ChapterApi::getMethodLabel();
		$chapter = [
			'name' => $chapter->name,
			'request' => [
				'url' => $url,
				'method' => $methodLabel[$method],
				'header' => $this->getHeader($chapter->id),
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
				'url'=>$this->getUrl(),
				'description' => $description
			],
			'response' => []
		];
		return $chapter;
	}

	public function getUrl(){

	}

	public function getHeader($chapterId){
		$chapterDemoService=new ChapterDemoService($chapterId);
		$data=$chapterDemoService->getChapterDemo(0,1,[ChapterApiParam::LOCATION_REQUEST_HEADER]);
		$reply = [];
		foreach ($data as $key =>$val){
			$reply[]=[
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
