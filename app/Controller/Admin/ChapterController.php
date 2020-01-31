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
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\App\Model\Logic\UserOperateLogic;
use W7\Http\Message\Server\Request;

/**
 * Class ChapterController
 * @package W7\App\Controller\Admin
 */
class ChapterController extends BaseController
{
	public function detail(Request $request)
	{
		$this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传',
		]);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$documentId = intval($request->input('document_id'));
		$document = DocumentLogic::instance()->getById($documentId);

		$chapter = ChapterLogic::instance()->getCatalog($documentId);

		$result = [
			'document' => [
				'id' => $document->id,
				'name' => $document->name,
			],
			'catalog' => $chapter,
			'acl' => [
				'has_manage' => $user->isManager
			]
		];

		return $this->data($result);
	}

	public function create(Request $request)
	{
		$this->validate($request, [
			'name' => 'string|required|max:30',
			'document_id' => 'required|integer|min:1',
			'parent_id' => 'required|integer|min:0',
			'is_dir' => 'required|boolean',
		], [
			'name.required' => '章节名称必填',
			'name.max' => '章节名最大３０个字符',
			'document_id.required' => '文档id必填',
			'document_id.min' => '文档id最小为0',
			'parent_id.required' => '父id必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$parentId = intval($request->post('parent_id'));
		$isDir = $request->post('is_dir');
		if (!empty($parentId)) {
			$parentChapter = ChapterLogic::instance()->getById($parentId);
			if (empty($parentChapter)) {
				throw new ErrorHttpException('父章节不存在');
			}
		}

		$documentId = intval($request->post('document_id'));
		$maxSort = Chapter::query()->where('document_id', '=', $documentId)->where('parent_id', '=', $parentId)->max('sort');
		$sort = intval($request->post('sort', ++$maxSort));
		$chapter = Chapter::query()->create([
			'name' => $request->post('name'),
			'sort' => $sort,
			'is_dir' => $isDir ? 1 : 0,
			'document_id' => $documentId,
			'parent_id' => $parentId,
		]);
		if (!$chapter) {
			throw new ErrorHttpException('章节添加失败');
		}

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $documentId,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::CREATE
		]);

		return $this->data($chapter->toArray());
	}

	public function update(Request $request)
	{
		$this->validate($request, [
			'name' => 'string|required|max:30',
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
		], [
			'name.required' => '章节名称必填',
			'name.max' => '章节名最大３０个字符',
			'chapter_id.required' => '文档id必填',
			'chapter_id.min' => '文档id最小为0',
			'document_id.required' => '文档id必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$parentId = $request->post('parent_id', null);
		$chapterId = intval($request->post('chapter_id'));
		$chapter = ChapterLogic::instance()->getById($chapterId);
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}
		if (isset($parentId)) {
			if ($parentId != 0) {
				$parentChapter = ChapterLogic::instance()->getById($parentId);
				if (!$parentChapter || $parentChapter->is_dir != Chapter::IS_DIR) {
					throw new ErrorHttpException('上级章节不存在');
				}
			}
			$chapter->parent_id = $parentId;
		}

		$chapter->name = $request->post('name');
		$chapter->sort = intval($request->post('sort'));

		$chapter->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::EDIT,
			'remark' => '编辑文档标题'
		]);

		return $this->data('success');
	}

	public function sort(Request $request)
	{
		$this->validate($request, [
			'target.chapter_id' => 'sometimes|integer',
			'target.position' => 'required|in:inner,before,after,move',
			'chapter_id' => 'required|integer',
			'document_id' => 'required|integer',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$targetChapter = ChapterLogic::instance()->getById($request->post('target')['chapter_id']);
		$chapter = ChapterLogic::instance()->getById($request->post('chapter_id'));

		$position = $request->post('target')['position'];

		if (empty($chapter)) {
			throw new ErrorHttpException('要移动的章节不存在');
		}

		if ($position == 'move') {
			$targetDocumentId = $request->post('target')['document_id'];
			$documentPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($targetDocumentId, $user->id);
			if (!$user->isFounder && !$documentPermission->isManager && !$documentPermission->isOperator) {
				throw new ErrorHttpException('您没有权限管理该文档');
			}

			$chapter->document_id = $targetDocumentId;
			$chapter->save();
		} else {
			if ($targetChapter->document_id != $request->post('document_id')) {
				throw new ErrorHttpException('只能移动到当前文档中的其它目录');
			}
		}

		//放入到目录节点中，但不存在排序
		if ($position == 'inner' || $position == 'move') {
			try {
				ChapterLogic::instance()->moveByChapter($chapter, $targetChapter);
			} catch (\Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
		} else {
			if (empty($targetChapter)) {
				throw new ErrorHttpException('要移到的章节不存在');
			}

			$chapter->parent_id = $targetChapter->parent_id;
			$chapter->save();

			try {
				ChapterLogic::instance()->sortByChapter($chapter, $targetChapter, $position);
			} catch (\Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
		}

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::EDIT,
			'remark' => '移动文档'
		]);

		return $this->data('success');
	}

	public function delete(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required',
			'document_id' => 'required|integer',
		], [
			'chapter_id.required' => '章节不存在',
			'document_id.required' => '文档id必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		if (!is_array($request->post('chapter_id'))) {
			$chapterId = intval($request->post('chapter_id'));
			$chapterId = [$chapterId];
		} else {
			$chapterId = $request->post('chapter_id');
		}

		try {
			$documentId = intval($request->post('document_id'));
			foreach ($chapterId as $id) {
				$id = intval($id);
				if (empty($id)) {
					continue;
				}
				ChapterLogic::instance()->deleteById($id);
				UserOperateLog::query()->create([
					'user_id' => $user->id,
					'document_id' => $documentId,
					'chapter_id' => $id,
					'operate' => UserOperateLog::DELETE
				]);
			}
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data('success');
	}

	public function save(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
		], [
			'chapter_id.required' => '文档id必填',
			'document_id.required' => '文档id必填',
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
			$chapter->content->save();
		} else {
			ChapterContent::query()->create([
				'chapter_id' => $chapterId,
				'content' => $request->post('content')
			]);
		}

		$chapter->updated_at = time();
		$chapter->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::EDIT,
			'remark' => '编辑文档内容'
		]);

		return $this->data('success');
	}

	public function content(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
		], [
			'chapter_id.required' => '文档id必填',
			'chapter_id.min' => '文档id最小为0',
			'document_id.required' => '文档id必填',
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

		$creator = UserOperateLogic::instance()->getByChapterAndOperate($chapterId, UserOperateLog::CREATE);
		if ($creator) {
			$author = $creator->user;
		} else {
			$author = $chapter->document->user;
		}
		$result = [
			'chapter_id' => $chapterId,
			'content' => $chapter->content->content,
			'author' => [
				'uid' => $author->id,
				'username' => $author->username,
			],
			'updated_at' => $chapter->updated_at->toDateTimeString()
		];

		return $this->data($result);
	}

	/**
	 * 设置章节目录默认显示文章内容
	 */
	public function defaultShow(Request $request) {
		$this->validate($request, [
			'chapter_id' => 'required',
			'show_chapter_id' => 'required',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('chapter_id'));
		$chapter = ChapterLogic::instance()->getById($chapterId);

		$showChapterId = intval($request->post('show_chapter_id'));
		$showChapter = ChapterLogic::instance()->getById($showChapterId);

		if (($chapterId && empty($chapter))|| empty($showChapter)) {
			throw new ErrorHttpException('您要操作的章节或是目录不存在');
		}

		if ($chapter && empty($chapter->is_dir)) {
			throw new ErrorHttpException('此操作只能设置目录的默认显示');
		}

		if ($chapterId == 0) {
			$chapter = $showChapter;
		}

		if (!empty($showChapter->is_dir)) {
			throw new ErrorHttpException('设置显示的章节不能为目录');
		}

		$chapter->default_show_chapter_id = $showChapterId;
		$chapter->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::EDIT,
			'remark' => '设置文档默认显示'
		]);

		return $this->data('success');
	}

	public function search(Request $request)
	{
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

		try {
			$result = ChapterLogic::instance()->searchChapter($id, $keywords);
			return $this->data($result);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}
