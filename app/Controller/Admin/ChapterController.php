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
use W7\App\Model\Service\ChapterRecordService;
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
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$parentId = intval($request->post('parent_id'));
		if (!empty($parentId)) {
			$parentChapter = ChapterLogic::instance()->getById($parentId);
			if (empty($parentChapter)) {
				throw new ErrorHttpException('父章节不存在');
			}
		}

		$isDir = $request->post('is_dir');
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
			'operate' => UserOperateLog::CREATE,
			'remark' => $user->username . '创建章节' . $chapter->name
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
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapter = ChapterLogic::instance()->getById($request->post('chapter_id'));
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}
		$parentId = $request->post('parent_id', null);
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
		$chapter->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::EDIT,
			'remark' => $user->username . '编辑章节' . $chapter->name . '基本信息'
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
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapter = ChapterLogic::instance()->getById($request->post('chapter_id'));
		if (empty($chapter)) {
			throw new ErrorHttpException('要移动的章节不存在');
		}

		$position = $request->post('target')['position'];
		$targetChapter = ChapterLogic::instance()->getById($request->post('target')['chapter_id']);

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
				if (empty($targetChapter)) {
					//找到该文档的根节点中的其中一个章节
					$targetChapter = Chapter::query()->where('document_id', $chapter->document_id)->where('parent_id', '=', 0)->first();
				}
				$targetChapter && ChapterLogic::instance()->moveByChapter($chapter, $targetChapter);
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

		if ($position != 'move') {
			$targetChapter && $targetChapter = ChapterLogic::instance()->getById($targetChapter->parent_id);
		}
		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::CHAPTER_MOVE,
			'remark' => $user->username . '移动章节' . $chapter->name . '到' . !empty($targetChapter) ? $targetChapter->name : '根节点'
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
		if (!$user->isOperator) {
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
				$chapter = ChapterLogic::instance()->getById($id);
				if ($chapter) {
					ChapterLogic::instance()->deleteById($id);

					UserOperateLog::query()->create([
						'user_id' => $user->id,
						'document_id' => $documentId,
						'chapter_id' => $id,
						'operate' => UserOperateLog::DELETE,
						'remark' => $user->username . '删除章节' . $chapter->name
					]);
				}
			}
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data('success');
	}

	/**
	 * @api {post} /chapter/save 文档内容-保存
	 * @apiName save
	 * @apiGroup Chapter
	 *
	 * @apiParam {Number} chapter_id 章节ID
	 * @apiParam {Number} document_id 文档ID
	 * @apiParam {Number} layout 文档类型 0：MARKDOWM文本，提交content 1：HTTP请求，提交record
	 * @apiParam {String} content 文档内容（layout为1时用record生成content，此字段提交无效）
	 * @apiParam {Array} record 请求记录
	 * @apiParam {Array} record.api 请求记录地址信息
	 * @apiParam {String} record.api.type 请求类型
	 * @apiParam {String} record.api.value 地址
	 * @apiParam {String} record.api.description 描述
	 * @apiParam {Array} record.apiHeader 请求头
	 * @apiParam {String} record.apiHeader.key 参数名称
	 * @apiParam {Number} record.apiHeader.must 是否必传
	 * @apiParam {String} record.apiHeader.description 参数描述
	 * @apiParam {String} record.apiHeader.value 参数示例值
	 * @apiParam {Array} record.apiParam 请求参数
	 * @apiParam {String} record.apiParam.key 参数名称
	 * @apiParam {String} record.apiParam.type 参数类型 int,string....
	 * @apiParam {Number} record.apiParam.must 是否必传
	 * @apiParam {String} record.apiParam.description 参数描述
	 * @apiParam {String} record.apiParam.value 参数示例值
	 * @apiParam {Array} record.apiParam.children 参数子类
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {status: true, code: 200, data: "success", message: "ok"}
	 */
	public function save(Request $request)
	{
		$LayoutLabel = array_keys(ChapterContent::getLayoutLabel());
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
			'layout' => 'in:' . implode(',', $LayoutLabel),
		], [
			'chapter_id.required' => '文档id必填',
			'document_id.required' => '文档id必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapter = ChapterLogic::instance()->getById(intval($request->post('chapter_id')));
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		$layout = $request->post('layout', 0);
		$content = $request->post('content', '');
		if ($layout == 1) {
			//如果是http类型
			$record = $request->post('record', []);
			$chapterRecord = new ChapterRecordService($record);
			$content = $chapterRecord->recordToMarkdown();
		}

		if (!empty($chapter->content)) {
			$chapter->content->content = $content;
			$chapter->content->save();
		} else {
			ChapterContent::query()->create([
				'chapter_id' => $chapter->id,
				'content' => $content
			]);
		}

		$chapter->updated_at = time();
		$chapter->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::EDIT,
			'remark' => $user->username . '编辑章节' . $chapter->name . '内容'
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
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapter = ChapterLogic::instance()->getById(intval($request->post('chapter_id')));
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		$creator = UserOperateLogic::instance()->getByChapterAndOperate($chapter->id, UserOperateLog::CREATE);
		if ($creator) {
			$author = $creator->user;
		} else {
			$author = $chapter->document->user;
		}
		$result = [
			'chapter_id' => $chapter->id,
			'name' => $chapter->name,
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
	 * @param Request $request
	 * @return array
	 */
	public function defaultShow(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required',
			'show_chapter_id' => 'required',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('chapter_id'));
		$chapter = ChapterLogic::instance()->getById($chapterId);

		$showChapterId = intval($request->post('show_chapter_id'));
		$showChapter = ChapterLogic::instance()->getById($showChapterId);

		if (($chapterId && empty($chapter)) || empty($showChapter)) {
			throw new ErrorHttpException('您要操作的章节或是目录不存在');
		}

		if ($chapter && empty($chapter->is_dir)) {
			throw new ErrorHttpException('此操作只能设置目录的默认显示');
		}
		if (!empty($showChapter->is_dir)) {
			throw new ErrorHttpException('设置显示的章节不能为目录');
		}

		if ($chapterId == 0) {
			//找到已存在的顶级默认文章
			$defaultShowChapter = Chapter::query()->where('document_id', '=', $showChapter->document_id)->where('parent_id', '=', 0)->where('default_show_chapter_id', '!=', 0)->first();
			if ($defaultShowChapter) {
				$defaultShowChapter->default_show_chapter_id = 0;
				$defaultShowChapter->save();
			}
			$chapter = $showChapter;
		}

		$chapter->default_show_chapter_id = $showChapterId;
		$chapter->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $showChapterId,
			'operate' => UserOperateLog::EDIT,
			'remark' => $user->username . '设置章节' . $chapter->name . '默认显示'
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

	public function copy(Request $request)
	{
		$params = $this->validate($request, [
			'parent_id' => 'required',
			'name' => 'required',
			'document_id' => 'required|integer|min:1',
			'chapter_id' => 'required|integer|min:1',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}
		$parentChapter = null;
		if ($params['parent_id']) {
			$parentChapter = ChapterLogic::instance()->getById($params['parent_id']);
			if (!$parentChapter) {
				throw new ErrorHttpException('目标章节不存在');
			}
		}
		$chapter = ChapterLogic::instance()->getById($params['chapter_id']);
		if (!$chapter) {
			throw new ErrorHttpException('章节不存在');
		}

		$maxSort = Chapter::query()->where('document_id', '=', $params['document_id'])->where('parent_id', '=', $params['parent_id'])->max('sort');
		$sort = intval($request->post('sort', ++$maxSort));

		$newChapter = new Chapter();
		$newChapter->parent_id = $params['parent_id'];
		$newChapter->name = $params['name'];
		$newChapter->document_id = $params['document_id'];
		$newChapter->sort = $sort;
		$newChapter->is_dir = $chapter->is_dir;
		$newChapter->save();

		$chapterContent = ChapterContent::query()->where('chapter_id', '=', $params['chapter_id'])->first();
		if ($chapterContent) {
			$newChapterContent = new ChapterContent();
			$newChapterContent->chapter_id = $newChapter->id;
			$newChapterContent->content = $chapterContent->content;
			$newChapterContent->layout = $chapterContent->layout;
			$newChapterContent->save();
		}

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $chapter->document_id,
			'chapter_id' => $chapter->id,
			'operate' => UserOperateLog::CHAPTER_COPY,
			'remark' => $user->username . '复制章节' . $chapter->name . '到' . !empty($parentChapter) ? $parentChapter->name : '根节点'
		]);

		return $this->data($newChapter->toArray());
	}
}
