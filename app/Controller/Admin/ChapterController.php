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

	/**
	 * @api {post} /chapter/create 文档（目录）-新增
	 * @apiName create
	 * @apiGroup Chapter
	 *
	 * @apiParam {Number} name 章节ID
	 * @apiParam {Number} document_id 目录ID
	 * @apiParam {Number} parent_id 附ID
	 * @apiParam {Number} is_dir 是否为目录
	 * @apiParam {Number} layout 0：markdown格式 1：http格式(新增文件时)
	 */
	public function create(Request $request)
	{
		$this->validate($request, [
			'name' => 'string|required|max:30',
			'document_id' => 'required|integer|min:1',
			'parent_id' => 'required|integer|min:0',
			'is_dir' => 'required|boolean',
			'layout' => 'integer'
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

		if (!$isDir) {
			//如果是非目录，创建关联表并且锁定类型
			$layout = $request->post('layout', 0);
			if (!empty($chapter->content)) {
				$chapter->content->content = '';
				$chapter->content->layout = $layout;
				$chapter->content->save();
			} else {
				ChapterContent::query()->create([
					'chapter_id' => $chapter->id,
					'content' => '',
					'layout' => $layout
				]);
			}
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
	 * @apiParam {String} record.api.method 请求方式
	 * @apiParam {String} record.api.url 地址
	 * @apiParam {String} record.api.description 描述
	 * @apiParam {Array} record.request 请求
	 * @apiParam {Array} record.request.location 请求类型1-11参考getLocationLabel
	 * @apiParam {String} record.request.location.id 参数id
	 * @apiParam {String} record.request.location.name 参数名称
	 * @apiParam {String} record.request.location.type 参数类型（location=1,7header的时候固定为string可不传） int,string...
	 * @apiParam {Number} record.request.location.enabled 是否必传
	 * @apiParam {String} record.request.location.description 参数描述
	 * @apiParam {String} record.request.location.default_value 参数示例值
	 * @apiParam {String} record.request.location.rule 生成规则
	 * @apiParam {Array} record.request.location.children 参数子类数组同父级
	 * @apiParam {String} apiExtend 扩展内容
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
			$chapterRecord = new ChapterRecordService($chapter->id, $record);
			$content = $chapterRecord->recordToMarkdown();
		}

		if (!empty($chapter->content)) {
			if ($chapter->content->layout != $layout) {
				throw new ErrorHttpException('不可更改文档类型');
			}
			$chapter->content->content = $content;
			$chapter->content->save();
		} else {
			ChapterContent::query()->create([
				'chapter_id' => $chapter->id,
				'content' => $content,
				'layout' => $layout
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

	/**
	 * @api {post} /chapter/content 文档内容-查看
	 * @apiName content
	 * @apiGroup Chapter
	 *
	 * @apiParam {Number} chapter_id 章节ID
	 * @apiParam {Number} document_id 文档ID
	 * @apiParam {Number} layout 文档类型 0：MARKDOWM文本，提交content 1：HTTP请求，提交record
	 * @apiParam {String} content 文档内容（layout为1时用record生成content，此字段提交无效）
	 * @apiParam {Array} record 请求记录
	 * @apiParam {Array} record.api 请求记录地址信息
	 * @apiParam {String} record.api.method 请求方式
	 * @apiParam {String} record.api.url 地址
	 * @apiParam {String} record.api.description 描述
	 * @apiParam {Array} record.apiHeader 请求头
	 * @apiParam {String} record.apiHeader.id 参数id
	 * @apiParam {String} record.apiHeader.name 参数名称
	 * @apiParam {Number} record.apiHeader.enabled 是否必传
	 * @apiParam {String} record.apiHeader.description 参数描述
	 * @apiParam {String} record.apiHeader.default_value 参数示例值
	 * @apiParam {String} record.apiHeader.rule 生成规则
	 * @apiParam {Array} record.apiParam 请求参数
	 * @apiParam {String} record.apiParam.id 参数id
	 * @apiParam {String} record.apiParam.name 参数名称
	 * @apiParam {String} record.apiParam.type 参数类型 int,string...
	 * @apiParam {Number} record.apiParam.enabled 是否必传
	 * @apiParam {String} record.apiParam.description 参数描述
	 * @apiParam {String} record.apiParam.default_value 参数示例值
	 * @apiParam {String} record.apiParam.rule 生成规则
	 * @apiParam {Array} record.apiParam.children 参数子类数组同父级
	 * @apiParam {Array} record.apiSuccess 返回参数
	 * @apiParam {String} record.apiSuccess.id 参数id
	 * @apiParam {String} record.apiSuccess.name 参数名称
	 * @apiParam {String} record.apiSuccess.type 参数类型 int,string...
	 * @apiParam {Number} record.apiSuccess.enabled 是否必传
	 * @apiParam {String} record.apiSuccess.description 参数描述
	 * @apiParam {String} record.apiSuccess.default_value 参数示例值
	 * @apiParam {String} record.apiSuccess.rule 生成规则
	 * @apiParam {Array} record.apiSuccess.children 参数子类数组同父级
	 * @apiParam {String} apiExtend 扩展内容
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"chapter_id":49,"name":"clj1","content":"- **接口说明：** 这是文档说明\n- **接口地址：** http:\/\/baidu.com\n- **请求方式：** ==GET==\n\n### 请求头\n\n参数名称            |必填 |描述                |示例值              \n|:-                 |:-:  |:-                  |:-                  \nAccess-Token        |False|这是头部说明1       |                    \nToken               |Ture |this is address     |                    \n\n### 请求参数\n\n参数名称            |类型    |必填 |描述                |示例值              \n|:-                 |:-:     |:-:  |:-                  |:-                  \nid                  |number  |False|这是参数详情1       |                    \nboday               |array   |False|这是参数详情3       |                    \n&emsp;a             |array   |Ture |这是二级参数a       |                    \n&emsp;&emsp;c       |string  |Ture |这是三级参数ac      |                    \n&emsp;&emsp;d       |string  |Ture |这是三级参数ad      |                    \n&emsp;b             |array   |Ture |这是二级参数b       |                    \n&emsp;&emsp;c       |string  |Ture |这是三级参数bc      |                    \n&emsp;&emsp;e       |array   |Ture |这是三级参数bd      |                    \n&emsp;&emsp;&emsp;ec|string  |Ture |这是四级参数ec      |                    \n&emsp;&emsp;&emsp;ed|string  |Ture |这是四级参数ed      |                    \n\n### 返回参数\n\n参数名称            |类型    |必填 |描述                |示例值              \n|:-                 |:-:     |:-:  |:-                  |:-                  \nid                  |number  |False|这是参数详情1       |                    \nboday               |array   |False|这是参数详情3       |                    \n&emsp;a             |array   |Ture |这是二级参数a       |                    \n&emsp;&emsp;c       |string  |Ture |这是三级参数ac      |                    \n&emsp;&emsp;d       |string  |Ture |这是三级参数ad      |                    \n&emsp;b             |array   |Ture |这是二级参数b       |                    \n&emsp;&emsp;c       |string  |Ture |这是三级参数bc      |                    \n&emsp;&emsp;e       |array   |Ture |这是三级参数bd      |                    \n&emsp;&emsp;&emsp;ec|string  |Ture |这是四级参数ec      |                    \n&emsp;&emsp;&emsp;ed|string  |Ture |这是四级参数ed      |                    \n\n### 示例说明\n\n>请求示例：\n\n```\n{\n    \"Header\":{\n        \"Token\":\"\",\n        \"Version\":\"3.2.0\",\n        \"SystemId\":100,\n        \"Timestamp\":1502870664\n    },\n    \"Body\":{\n        \"Mobile\":\"18520322032\",\n        \"Password\":\"acb000000\"\n    }\n}\n\n```","layout":1,"author":{"uid":1,"username":"admin"},"updated_at":"2020-04-07 13:55:35","record":{"api":{"type":"GET","value":"http:\/\/baidu.com","description":"这是文档说明"},"apiHeader":[{"key":"Access-Token","description":"这是头部说明1","enabled":0,"value":""},{"type":"json","key":"Token","description":"this is address","enabled":1,"value":""}],"apiParam":[{"type":"number","key":"id","description":"这是参数详情1","enabled":0,"value":""},{"type":"array","key":"boday","description":"这是参数详情3","enabled":0,"value":"","children":[{"type":"array","key":"a","description":"这是二级参数a","enabled":1,"value":"","children":[{"type":"string","key":"c","description":"这是三级参数ac","enabled":1,"value":""},{"type":"string","key":"d","description":"这是三级参数ad","enabled":1,"value":"","children":[]}]},{"type":"array","key":"b","description":"这是二级参数b","enabled":1,"value":"","children":[{"type":"string","key":"c","description":"这是三级参数bc","enabled":1,"value":""},{"type":"array","key":"e","description":"这是三级参数bd","enabled":1,"value":"","children":[{"type":"string","key":"ec","description":"这是四级参数ec","enabled":1,"value":""},{"type":"string","key":"ed","description":"这是四级参数ed","enabled":1,"value":"","children":[]}]}]}]}],"apiSuccess":[{"type":"number","key":"id","description":"这是参数详情1","enabled":0,"value":""},{"type":"array","key":"boday","description":"这是参数详情3","enabled":0,"value":"","children":[{"type":"array","key":"a","description":"这是二级参数a","enabled":1,"value":"","children":[{"type":"string","key":"c","description":"这是三级参数ac","enabled":1,"value":""},{"type":"string","key":"d","description":"这是三级参数ad","enabled":1,"value":"","children":[]}]},{"type":"array","key":"b","description":"这是二级参数b","enabled":1,"value":"","children":[{"type":"string","key":"c","description":"这是三级参数bc","enabled":1,"value":""},{"type":"array","key":"e","description":"这是三级参数bd","enabled":1,"value":"","children":[{"type":"string","key":"ec","description":"这是四级参数ec","enabled":1,"value":""},{"type":"string","key":"ed","description":"这是四级参数ed","enabled":1,"value":"","children":[]}]}]}]}],"apiExtend":"### 示例说明\n\n>请求示例：\n\n```\n{\n    \"Header\":{\n        \"Token\":\"\",\n        \"Version\":\"3.2.0\",\n        \"SystemId\":100,\n        \"Timestamp\":1502870664\n    },\n    \"Body\":{\n        \"Mobile\":\"18520322032\",\n        \"Password\":\"acb000000\"\n    }\n}\n\n```"}},"message":"ok"}
	 */
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
			'layout' => $chapter->content->layout,
			'author' => [
				'uid' => $author->id,
				'username' => $author->username,
			],
			'updated_at' => $chapter->updated_at->toDateTimeString()
		];
		if ($chapter->content->layout == 1 && $chapter->record) {
			$result['record'] = json_decode($chapter->record->record);
		}

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
			if ($chapterContent->layout == 1) {
				//如果是HTTP类型@todo
			}
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
