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

namespace W7\App\Controller\Document;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends BaseController
{
	/**
	 * 某一个文档的目录
	 * @param Request $request
	 * @return array
	 */
	public function catalog(Request $request)
	{
		$this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法'
		]);

		try {
			$result = ChapterLogic::instance()->getCatalog($request->input('document_id'));
			return $this->data($result);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function detail(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
			'chapter_id' => 'required|integer|min:1'
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法',
			'chapter_id.required' => '章节id必填',
			'chapter_id.integer' => '章节id非法'
		]);

		try {
			$chapter = ChapterLogic::instance()->getById($params['chapter_id'], $params['document_id']);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		if (!$chapter) {
			throw new ErrorHttpException('该章节不存在！');
		}

		$document = DocumentLogic::instance()->getById($params['document_id']);

		$result = [
			'id' => $chapter->id,
			'parent_id' => $chapter->parent_id,
			'name' => $chapter->name,
			'document_id' => $chapter->document_id,
			'created_at' => $chapter->created_at->toDateTimeString(),
			'updated_at' => $chapter->updated_at->toDateTimeString(),
			'content' => $chapter->content->content,
			'prev_item' => [
				'id' => $chapter->prevItem->id ?? '',
				'name' => $chapter->prevItem->name ?? '',
			],
			'next_item' => [
				'id' => $chapter->nextItem->id ?? '',
				'name' => $chapter->nextItem->name ?? '',
			],
			'author' => [
				'uid' => $document->user->id,
				'username' => $document->user->username,
			]
		];

		return $this->data($result);
	}

	public function search(Request $request)
	{
		$this->validate($request, [
			'keywords' => 'required',
		], [
			'keywords.required' => '关键字必填',
		]);

		$keyword = $request->input('keywords');
		$documentId = intval($request->input('document_id'));

	}
}
