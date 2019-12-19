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
		$id = intval($request->input('document_id'));

		if (!$id) {
			throw new ErrorHttpException('文档不存在或是已经被删除');
		}

		try {
			$result = ChapterLogic::instance()->getCatalog($id);
			return $this->data($result);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function detail(Request $request)
	{
		$id = intval($request->input('id'));
		$documentId = intval($request->input('document_id'));

		if (empty($id) || empty($documentId)) {
			throw new ErrorHttpException('章节不存在或是已经被删除');
		}

		try {
			$chapter = ChapterLogic::instance()->getById($id, $documentId);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		if (!$chapter) {
			throw new ErrorHttpException('该章节不存在！');
		}

		$document = DocumentLogic::instance()->getById($documentId);

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
