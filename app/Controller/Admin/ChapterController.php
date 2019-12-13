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

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends BaseController
{
	public function detail(Request $request)
	{
		$this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传',
		]);
		$documentId = intval($request->input('document_id'));
		$document = DocumentLogic::instance()->getById($documentId);

		$chapter = ChapterLogic::instance()->getCatalog($documentId);

		$result = [
			'document' => [
				'id' => $document->id,
				'name' => $document->name,
			],
			'catalog' => $chapter,
		];
		return $this->data($result);
	}

	public function create(Request $request)
	{
		$this->validate($request, [
			'name' => 'string|required|max:30',
			'sort' => 'required|integer|min:0',
			'document_id' => 'required|integer|min:1',
			'parent_id' => 'required|integer|min:0',
		], [
			'name.required' => '章节名称必填',
			'name.max' => '章节名最大３０个字符',
			'sort.min' => '排序最小值为０',
			'sort.required' => '排序必填',
			'document_id.required' => '文档id必填',
			'document_id.min' => '文档id最小为0',
			'parent_id.required' => '父id必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$parentId = intval($request->post('parent_id'));
		if (!empty($parentId)) {
			$parentChapter = ChapterLogic::instance()->getById($parentId);
			if (empty($parentChapter)) {
				throw new ErrorHttpException('父章节不存在');
			}
		}

		Chapter::query()->create([
			'name' => $request->post('name'),
			'sort' => intval($request->post('sort')),
			'document_id' => intval($request->post('document_id')),
			'parent_id' => $parentId,
		]);

		return $this->data('success');
	}

	public function update(Request $request)
	{
		$this->validate($request, [
			'name' => 'string|required|max:30',
			'sort' => 'required|integer|min:0',
			'id' => 'required|integer|min:1',
		], [
			'name.required' => '章节名称必填',
			'name.max' => '章节名最大３０个字符',
			'sort.min' => '排序最小值为０',
			'sort.required' => '排序必填',
			'id.required' => '文档id必填',
			'id.min' => '文档id最小为0',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('id'));
		$chapter = ChapterLogic::instance()->getById($chapterId);

		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		$chapter->name = $request->post('name');
		$chapter->sort = intval($request->post('sort'));

		$chapter->save();

		return $this->data('success');
	}

	public function delete(Request $request)
	{
		$this->validate($request, [
			'id' => 'required|integer',
		], [
			'id.required' => 'id is required',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('id'));

		try {
			ChapterLogic::instance()->deleteById($chapterId);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data('success');
	}

	public function save(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'layout' => 'required|integer|min:1',
		], [
			'chapter_id.required' => '文档id必填',
			'layout' => '文档布局必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('chapter_id'));
		$chapter = ChapterLogic::instance()->getById($chapterId);
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		if (!empty($chapter->content)) {
			$chapter->content->content =  $request->post('content');
			$chapter->content->layout =  intval($request->post('layout'));
			$chapter->content->save();
		} else {
			ChapterContent::query()->create([
				'chapter_id' => $chapterId,
				'content' => $request->post('content'),
				'layout' => intval($request->post('layout')),
			]);
		}

		$chapter->updated_at = time();
		$chapter->save();

		return $this->data('success');
	}

	public function content(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
		], [
			'chapter_id.required' => '文档id必填',
			'chapter_id.min' => '文档id最小为0',
		]);
		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('chapter_id'));
		$chapter = ChapterLogic::instance()->getById($chapterId);

		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		$result = [
			'chapter_id' => $chapterId,
			'content' => $chapter->content->content,
			'layout' => $chapter->content->layout,
			'author' => [
				'uid' => $chapter->document->user->id,
				'username' => $chapter->document->user->username,
			]
		];

		return $this->data($result);
	}

	public function searchChapter(Request $request)
	{
		try {
			$this->validate($request, [
				'keywords' => 'required',
				'document_id' => 'required|integer|min:1',
			], [
				'document_id.required' => '文档id必填',
				'document_id.min' => '文档id最小为0',
				'keywords.required' => '关键字必填',
			]);
			$id = $request->input('document_id');
			$keywords = $request->input('keywords');
			$result = $this->logic->searchChapter($id, $keywords);
			return $this->success($result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
