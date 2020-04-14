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
use W7\App\Model\Logic\Document\ChapterContentLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\App\Model\Logic\UserOperateLogic;
use W7\App\Model\Logic\Document\ChapterApi\ChapterRecordLogic;
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
			if (!$parentChapter->is_dir) {
				throw new ErrorHttpException('父章节不是目录，不可以新增子章节或者文档');
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

		$layout = $request->post('layout', 0);
		if ($layout) {
			//如果是非默认类型，新建时锁定类型
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
	 * @apiParam {String} record.api.tab_location tab页上次指定记录head,query,body
	 * @apiParam {String} record.api.body_param_location body_param默认类型
	 * @apiParam {Array} record.body 请求
	 * @apiParam {Array} record.body.location 请求类型1-11参考getLocationLabel，bodyparam请求参数默认3返回参数默认10
	 * @apiParam {String} record.body.location.id 参数id
	 * @apiParam {String} record.body.location.name 参数名称
	 * @apiParam {String} record.body.location.type 参数类型（location=1,7header的时候固定为string可不传） int,string...
	 * @apiParam {Number} record.body.location.enabled 是否必传
	 * @apiParam {String} record.body.location.description 参数描述
	 * @apiParam {String} record.body.location.default_value 参数示例值
	 * @apiParam {String} record.body.location.rule 生成规则
	 * @apiParam {Array} record.body.location.children 参数子类数组同父级
	 * @apiParam {String} apiExtend 扩展内容
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {status: true, code: 200, data: "success", message: "ok"}
	 */
	public function save(Request $request)
	{
		$layoutLabel = array_keys(ChapterContentLogic::instance()->getLayoutLabel());
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
			'layout' => 'in:' . implode(',', $layoutLabel),
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
			$chapterRecord = new ChapterRecordLogic($chapter->id);
			$content = $chapterRecord->recordToMarkdown($record);
		}

		if (!empty($chapter->content)) {
			if ($chapter->content->layout != $layout) {
				throw new ErrorHttpException('文档类型不可更改');
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
	 *
	 * @apiSuccess {Number} chapter_id 章节ID
	 * @apiSuccess {Number} document_id 文档ID
	 * @apiSuccess {Number} layout 文档类型 0：MARKDOWM文本，提交content 1：HTTP请求，提交record
	 * @apiSuccess {String} content 文档内容（layout为1时用record生成content，此字段提交无效）
	 * @apiSuccess {Array} record 请求记录
	 * @apiSuccess {Array} record.api 请求记录地址信息
	 * @apiSuccess {String} record.api.method 请求方式
	 * @apiSuccess {String} record.api.url 地址
	 * @apiSuccess {String} record.api.description 描述
	 * @apiSuccess {String} record.api.tab_location tab页上次指定记录head,query,body
	 * @apiSuccess {String} record.api.body_param_location body_param默认类型
	 * @apiSuccess {Array} record.body 请求
	 * @apiSuccess {Array} record.body.location 请求类型1-11参考getLocationLabel，bodyparam请求参数默认3返回参数默认10
	 * @apiSuccess {String} record.body.location.id 参数id
	 * @apiSuccess {String} record.body.location.name 参数名称
	 * @apiSuccess {String} record.body.location.type 参数类型（location=1,7header的时候固定为string可不传） int,string...
	 * @apiSuccess {Number} record.body.location.enabled 是否必传
	 * @apiSuccess {String} record.body.location.description 参数描述
	 * @apiSuccess {String} record.body.location.default_value 参数示例值
	 * @apiSuccess {String} record.body.location.rule 生成规则
	 * @apiSuccess {Array} record.body.location.children 参数子类数组同父级
	 * @apiSuccess {String} apiExtend 扩展内容
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"chapter_id":49,"name":"clj1","content":"- **接口说明：** 这是文档说明\n- **接口地址：** http:\/\/baidu.com\n- **请求方式：** ==GET==\n- **状态码：** ==200==\n\n### Request.Query.Path\n\n参数名称 |类型 |必填 |描述 |示例值 |生成规则 \n|:- |:-: |:-: |:- |:- |:- \npath参数 |String |False|这是地址栏参数 | | \n### Request.Header\n\n参数名称 |类型 |必填 |描述 |示例值 |生成规则 \n|:- |:-: |:-: |:- |:- |:- \nAccess-Token |String |False|这是头部说明1 | |12321 \n ec |String |False|这是四级参数ec | | \n ed |String |False|这是四级参数ed | | \nToken |Number |False|this is address | | \n### Request.Query.String\n\n参数名称 |类型 |必填 |描述 |示例值 |生成规则 \n|:- |:-: |:-: |:- |:- |:- \nid |String |False|这是参数详情1 | |12321 \nboday |Array |False|这是参数详情3 | | \n a |Number |False|这是二级参数a | | \n  c |String |False|这是三级参数ac | | \n  d |String |False|这是三级参数ad | | \n b |Array |False|这是二级参数b | | \n  c |String |False|这是三级参数bc | | \n  e |Array |False|这是三级参数bd | | \n   ec|String |False|这是四级参数ec | | \n   ed|String |False|这是四级参数ed | | \n### Request.Body.form-data\n\n参数名称 |类型 |必填 |描述 |示例值 |生成规则 \n|:- |:-: |:-: |:- |:- |:- \nAccess-Token |String |False|这是头部说明3 | |12321 \n### Reponse.Header\n\n参数名称 |类型 |必填 |描述 |示例值 |生成规则 \n|:- |:-: |:-: |:- |:- |:- \nAccess-Token |String |False|这是response头部说明7| |12321 \n### Reponse.Body.raw\n\n参数名称 |类型 |必填 |描述 |示例值 |生成规则 \n|:- |:-: |:-: |:- |:- |:- \n4444 |Null |False| | | \na |Number |False| |1 | \nb |Array |False| |[\"1\",\"1\"] | \n  |Number |False| |1 | \n  |Number |False| |1 | \nc |Object |False| | | \n a |Array |False| |[\"a\"] | \n b |Object |False| | | \n  0 |String |False| |b | \n  d |Array |False| |[\"4\",\"5\"] | \n    |Number |False| |4 | \n    |Number |False| |5 | \ne |Array |False| | |2 \n a |Array |False| |[\"1\"] | \n b |Array |False| |[\"2\",null] |+1 \n c |Array |False| |[\"3\"] | \n f |Array |False| |[\"1\",\"2\"] | \n   |Number |False| |1 | \n   |Number |False| |2 | \n d |Object |False| | | \n  a |Number |False| |1 | \n  b |Number |False| |2 | \n\n### 示例说明\n\n>请求示例：\n\n```\n{\n \"Header\":{\n \"Token\":\"\",\n \"Version\":\"3.2.0\",\n \"SystemId\":100,\n \"Timestamp\":1502870664\n },\n \"Body\":{\n \"Mobile\":\"18520322032\",\n \"Password\":\"acb000000\"\n }\n}\n\n```","layout":1,"author":{"uid":1,"username":"admin"},"updated_at":"2020-04-08 16:42:00","record":{"api":{"id":1,"chapter_id":49,"url":"http:\/\/baidu.com","method":1,"status_code":0,"description":"这是文档说明","body_param_location":3},"body":{"1":[{"id":2,"chapter_id":49,"parent_id":0,"location":1,"type":1,"name":"Access-Token","description":"这是头部说明1","enabled":1,"default_value":"","rule":"12321","children":[{"id":3,"chapter_id":49,"parent_id":2,"location":1,"type":1,"name":"ec","description":"这是四级参数ec","enabled":1,"default_value":"","rule":"","children":[]},{"id":4,"chapter_id":49,"parent_id":2,"location":1,"type":1,"name":"ed","description":"这是四级参数ed","enabled":1,"default_value":"","rule":"","children":[]}]},{"id":5,"chapter_id":49,"parent_id":0,"location":1,"type":2,"name":"Token","description":"this is address","enabled":1,"default_value":"","rule":"","children":[]}],"2":[{"id":6,"chapter_id":49,"parent_id":0,"location":2,"type":1,"name":"id","description":"这是参数详情1","enabled":1,"default_value":"","rule":"12321","children":[]},{"id":7,"chapter_id":49,"parent_id":0,"location":2,"type":5,"name":"boday","description":"这是参数详情3","enabled":1,"default_value":"","rule":"","children":[{"id":8,"chapter_id":49,"parent_id":7,"location":2,"type":2,"name":"a","description":"这是二级参数a","enabled":1,"default_value":"","rule":"","children":[{"id":9,"chapter_id":49,"parent_id":8,"location":2,"type":1,"name":"c","description":"这是三级参数ac","enabled":1,"default_value":"","rule":"","children":[]},{"id":10,"chapter_id":49,"parent_id":8,"location":2,"type":1,"name":"d","description":"这是三级参数ad","enabled":1,"default_value":"","rule":"","children":[]}]},{"id":11,"chapter_id":49,"parent_id":7,"location":2,"type":5,"name":"b","description":"这是二级参数b","enabled":1,"default_value":"","rule":"","children":[{"id":12,"chapter_id":49,"parent_id":11,"location":2,"type":1,"name":"c","description":"这是三级参数bc","enabled":1,"default_value":"","rule":"","children":[]},{"id":13,"chapter_id":49,"parent_id":11,"location":2,"type":5,"name":"e","description":"这是三级参数bd","enabled":1,"default_value":"","rule":"","children":[{"id":14,"chapter_id":49,"parent_id":13,"location":2,"type":1,"name":"ec","description":"这是四级参数ec","enabled":1,"default_value":"","rule":"","children":[]},{"id":15,"chapter_id":49,"parent_id":13,"location":2,"type":1,"name":"ed","description":"这是四级参数ed","enabled":1,"default_value":"","rule":"","children":[]}]}]}]}],"3":[{"id":16,"chapter_id":49,"parent_id":0,"location":3,"type":1,"name":"Access-Token","description":"这是头部说明3","enabled":1,"default_value":"","rule":"12321","children":[]}],"7":[{"id":17,"chapter_id":49,"parent_id":0,"location":7,"type":1,"name":"Access-Token","description":"这是response头部说明7","enabled":1,"default_value":"","rule":"12321","children":[]}],"10":[{"id":18,"chapter_id":49,"parent_id":0,"location":10,"type":8,"name":"4444","description":"","enabled":1,"default_value":"","rule":"","children":[]},{"id":19,"chapter_id":49,"parent_id":0,"location":10,"type":2,"name":"a","description":"","enabled":1,"default_value":"1","rule":"","children":[]},{"id":20,"chapter_id":49,"parent_id":0,"location":10,"type":5,"name":"b","description":"","enabled":1,"default_value":"[\"1\",\"1\"]","rule":"","children":[{"id":21,"chapter_id":49,"parent_id":20,"location":10,"type":2,"name":"","description":"","enabled":1,"default_value":"1","rule":"","children":[]},{"id":22,"chapter_id":49,"parent_id":20,"location":10,"type":2,"name":"","description":"","enabled":1,"default_value":"1","rule":"","children":[]}]},{"id":23,"chapter_id":49,"parent_id":0,"location":10,"type":4,"name":"c","description":"","enabled":1,"default_value":"","rule":"","children":[{"id":24,"chapter_id":49,"parent_id":23,"location":10,"type":5,"name":"a","description":"","enabled":1,"default_value":"[\"a\"]","rule":"","children":[]},{"id":25,"chapter_id":49,"parent_id":23,"location":10,"type":4,"name":"b","description":"","enabled":1,"default_value":"","rule":"","children":[{"id":26,"chapter_id":49,"parent_id":25,"location":10,"type":1,"name":"0","description":"","enabled":1,"default_value":"b","rule":"","children":[]},{"id":27,"chapter_id":49,"parent_id":25,"location":10,"type":5,"name":"d","description":"","enabled":1,"default_value":"[\"4\",\"5\"]","rule":"","children":[{"id":28,"chapter_id":49,"parent_id":27,"location":10,"type":2,"name":"","description":"","enabled":1,"default_value":"4","rule":"","children":[]},{"id":29,"chapter_id":49,"parent_id":27,"location":10,"type":2,"name":"","description":"","enabled":1,"default_value":"5","rule":"","children":[]}]}]}]},{"id":30,"chapter_id":49,"parent_id":0,"location":10,"type":5,"name":"e","description":"","enabled":1,"default_value":"","rule":"2","children":[{"id":31,"chapter_id":49,"parent_id":30,"location":10,"type":5,"name":"a","description":"","enabled":1,"default_value":"[\"1\"]","rule":"","children":[]},{"id":32,"chapter_id":49,"parent_id":30,"location":10,"type":5,"name":"b","description":"","enabled":1,"default_value":"[\"2\",null]","rule":"+1","children":[]},{"id":33,"chapter_id":49,"parent_id":30,"location":10,"type":5,"name":"c","description":"","enabled":1,"default_value":"[\"3\"]","rule":"","children":[]},{"id":34,"chapter_id":49,"parent_id":30,"location":10,"type":5,"name":"f","description":"","enabled":1,"default_value":"[\"1\",\"2\"]","rule":"","children":[{"id":35,"chapter_id":49,"parent_id":34,"location":10,"type":2,"name":"","description":"","enabled":1,"default_value":"1","rule":"","children":[]},{"id":36,"chapter_id":49,"parent_id":34,"location":10,"type":2,"name":"","description":"","enabled":1,"default_value":"2","rule":"","children":[]}]},{"id":37,"chapter_id":49,"parent_id":30,"location":10,"type":4,"name":"d","description":"","enabled":1,"default_value":"","rule":"","children":[{"id":38,"chapter_id":49,"parent_id":37,"location":10,"type":2,"name":"a","description":"","enabled":1,"default_value":"1","rule":"","children":[]},{"id":39,"chapter_id":49,"parent_id":37,"location":10,"type":2,"name":"b","description":"","enabled":1,"default_value":"2","rule":"","children":[]}]}]}],"12":[{"id":1,"chapter_id":49,"parent_id":0,"location":12,"type":1,"name":"path参数","description":"这是地址栏参数","enabled":1,"default_value":"","rule":"","children":[]}]},"extend":"### 示例说明\n\n>请求示例：\n\n```\n{\n \"Header\":{\n \"Token\":\"\",\n \"Version\":\"3.2.0\",\n \"SystemId\":100,\n \"Timestamp\":1502870664\n },\n \"Body\":{\n \"Mobile\":\"18520322032\",\n \"Password\":\"acb000000\"\n }\n}\n\n```"}},"message":"ok"}
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
		if ($chapter->content->layout == 1) {
			$obj = new ChapterRecordLogic($chapter->id);
			$result['record'] = $obj->showRecord();
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

		idb()->beginTransaction();
		try {
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
					//如果是HTTP类型
					$obj = new ChapterRecordLogic($chapter->id);
					$obj->copyRecord($newChapter->id);
				}
			}

			UserOperateLog::query()->create([
				'user_id' => $user->id,
				'document_id' => $chapter->document_id,
				'chapter_id' => $chapter->id,
				'operate' => UserOperateLog::CHAPTER_COPY,
				'remark' => $user->username . '复制章节' . $chapter->name . '到' . !empty($parentChapter) ? $parentChapter->name : '根节点'
			]);
			idb()->commit();
			return $this->data($newChapter->toArray());
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage(), $e->getCode());
		}
	}
}
