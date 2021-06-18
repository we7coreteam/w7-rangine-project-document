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
use W7\App\Model\Entity\Document\History\HistoryChapter;
use W7\App\Model\Logic\Document\HistoryChapterLogic;
use W7\App\Model\Logic\Document\HistoryLogic;
use W7\Http\Message\Server\Request;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Setting;
use W7\App\Model\Logic\UserOperateLogic;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Logic\Document\ChapterApi\ChapterRecordLogic;

class HistoryChapterController extends BaseController
{
	/**
	 * @api {get} document/history/chapter/list 文档历史版本-文档目录
	 * @apiName list
	 * @apiGroup historyChapter
	 *
	 * @apiParam {Number} document_id 文档id
	 * @apiParam {Number} history_id 版本id
	 *
	 * @apiSuccess {Number} id 历史版本章节id
	 * @apiSuccess {Number} chapter_id 章节id
	 * @apiSuccess {String} name 章节名称
	 * @apiSuccess {Boolean} is_dir 是否是目录 true是false否
	 * @apiSuccess {Object} children 子章节
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":[{"id":14,"chapter_id":2969,"name":"111","sort":2,"parent_id":0,"is_dir":true,"default_show_chapter_id":0,"children":[{"id":13,"chapter_id":2968,"name":"123","sort":31,"parent_id":2969,"is_dir":false,"default_show_chapter_id":0,"children":[],"level":2},{"id":17,"chapter_id":2972,"name":"123","sort":32,"parent_id":2969,"is_dir":false,"default_show_chapter_id":0,"children":[],"level":2}],"level":1},{"id":15,"chapter_id":2970,"name":"123","sort":3,"parent_id":0,"is_dir":false,"default_show_chapter_id":0,"children":[],"level":1},{"id":16,"chapter_id":2971,"name":"test","sort":4,"parent_id":0,"is_dir":false,"default_show_chapter_id":0,"children":[],"level":1}],"message":"ok"}
	 */
	public function catalog(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
			'history_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法',
			'history_id.required' => '历史版本id必填',
			'history_id.integer' => '历史版本id非法'
		]);

		try {
			$result = HistoryChapterLogic::instance()->getCatalog($params['history_id'], $params['document_id']);

			$user = $request->getAttribute('user');

			if (empty($user->isReader)) {
				throw new ErrorHttpException('当前账户无权限阅读该文档', [], Setting::ERROR_NO_POWER);
			}
			return $this->data($result);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	/**
	 * @api {get} document/history/chapter/detail 文档历史版本-章节内容
	 * @apiName detail
	 * @apiGroup historyChapter
	 *
	 * @apiParam {Number} history_chapter_id 历史版本章节id
	 * @apiParam {Number} document_id 文档id
	 * @apiParam {Number} history_id 版本id
	 *
	 * @apiSuccess {String} name 章节名称
	 * @apiSuccess {Text} content 章节内容
	 * @apiSuccess {String} author.username 作者名称
	 * @apiSuccess {Object} document 文档信息
	 * @apiSuccess {Object} api 接口文档信息
	 * @apiSuccess {String} navigation 面包屑
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2969,"parent_id":0,"name":"111","document_id":1298,"created_at":"2021-06-17 18:16:26","updated_at":"2021-06-17 18:16:26","content":null,"author":{"uid":1,"username":"admin"},"document":{"id":7,"document_id":1298,"name":"123","creator_id":1,"created_at":"2021-06-17 18:16:26","updated_at":"1623924986"},"api":null,"navigation":""},"message":"ok"}
	 */
	public function detail(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
			'history_chapter_id' => 'required|integer|min:1',
			'history_id' => 'required|integer|min:1'
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法',
			'history_chapter_id.required' => '章节id必填',
			'history_chapter_id.integer' => '章节id非法',
			'history_id.required' => '历史版本id必填',
			'history_id.integer' => '历史版本id非法',
		]);

		try {
			$chapter = HistoryChapter::query()->where([
				['id', '=', $params['history_chapter_id']],
				['document_id', '=', $params['document_id']],
				['history_id', '=', $params['history_id']]
			])->first();

			$user = $request->getAttribute('user');
			if (empty($user->isReader)) {
				throw new ErrorHttpException('当前账户无权限阅读该文档', [], Setting::ERROR_NO_POWER);
			}
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		if (!$chapter) {
			throw new ErrorHttpException('该章节不存在！', [], Setting::ERROR_NO_FIND);
		}

		$document = HistoryLogic::instance()->getById($params['history_id'], $params['document_id']);
		$creator = UserOperateLogic::instance()->getByChapterAndOperate($chapter->chapter_id, UserOperateLog::CREATE);
		if ($creator) {
			$author = $creator->user;
		} else {
			$author = $document->user;
		}

		$api = null;

		if ($chapter->content->layout == ChapterContent::LAYOUT_HTTP) {
			$api = ChapterApi::query()->where('chapter_id', $chapter->chapter_id)->first();
		}

		$result = [
			'id' => $chapter->chapter_id,
			'parent_id' => $chapter->parent_id,
			'name' => $chapter->name,
			'document_id' => $chapter->document_id,
			'created_at' => $chapter->created_at->toDateTimeString(),
			'updated_at' => $chapter->updated_at->toDateTimeString(),
			'content' => $chapter->content->content,
			'author' => [
				'uid' => $author->id,
				'username' => $author->username,
			],
			'document' => $document,
			'api' => $api,
			'navigation' => $this->buildNavigationSun($chapter->chapter_id, $params['history_id'])
		];

		$showRecord = $request->post('show_record', 0);
		if ($showRecord && $chapter->content->layout == 1) {
			$obj = new ChapterRecordLogic($chapter->chapter_id);
			$result['record'] = $obj->showRecord();
		}

		return $this->data($result);
	}

	public function buildNavigationSun($chapterId, $history_id, $str = '', $i = 0)
	{
		$i++;
		if ($i > 50) {
			//循环大于100，不再处理
			return $str;
		}
		$chapter = HistoryChapter::query()->where('chapter_id', $chapterId)->where('history_id', $history_id)->first();
		if ($chapter) {
			if (!$str) {
				//如果是根级
				$str = $chapter->name;
			} else {
				//如果是上级
				$str = $chapter->name . '>' . $str;
			}
			if ($chapter->parent_id) {
				$str = $this->buildNavigationSun($chapter->parent_id, $history_id, $str);
			}
		}
		return $str;
	}
}
