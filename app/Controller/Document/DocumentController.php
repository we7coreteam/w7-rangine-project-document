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
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Setting;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends BaseController
{
	/**
	 * @api {post} /document/all 全部文档接口
	 * @apiName  all
	 * @apiGroup Document
	 *
	 * @apiParam {Number} user_id 用户ID
	 *
	 * @apiSuccess {String} name 文档名称
	 * @apiSuccess {String} time_str 文档创建时间
	 * @apiSuccess {Number} created_at 文档创建时间戳
	 */
	public function all(Request $request)
	{
		$page = intval($request->input('page', 1));
		$pageSize = intval($request->input('page_size', 10));
		$params = $this->validate($request, [
			'user_id' => 'integer|min:1',
		], [
			'user_id.required' => '用户id必填',
			'user_id.integer' => '用户id非法'
		]);
		$query = Document::query();
		if (!empty($params['user_id'])) {
			$query->where('creator_id', $params['user_id']);
		}
		$query->where('is_public', '=', 1);
		$list = $query->select('id', 'name', 'cover', 'is_public', 'created_at')
			->orderByDesc('id')
			->paginate($pageSize, '*', 'page', $page)->toArray();
		return $this->data($list);
	}

	public function detail(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法'
		]);
		$res = DocumentLogic::instance()->getById($params['document_id']);
		if (!$res) {
			throw new ErrorHttpException('当前文档不存在', [], Setting::ERROR_NO_POWER);
		}

		$user = $request->getAttribute('user');
		if (empty($user->isReader)) {
			throw new ErrorHttpException('当前账户无权限阅读该文档', [], Setting::ERROR_NO_POWER);
		}
		if ($user && !empty($user->id)) {
			UserOperateLog::query()->create([
				'user_id' => $user->id,
				'document_id' => $params['document_id'],
				'chapter_id' => 0,
				'operate' => UserOperateLog::PREVIEW
			]);
		}

		if ($res->is_history == Document::IS_HISTORY_YES) {
			$res->increment('browse_num', 1);
			$res->save();
		}
		return $this->data($res);
	}

	/**
	 * @api {get} document/statistics 文档-文档统计
	 * @apiName statistics
	 * @apiGroup Document
	 *
	 * @apiParam {Number} document_id 文档id
	 *
	 * @apiSuccess {Number} browse_num 浏览次数
	 * @apiSuccess {Number} history_num 编辑次数
	 * @apiSuccess {String} last_update_time 上次修改时间
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"browse_num":2,"history_num":3,"last_update_time":"2021-06-17 18:16:26"},"message":"ok"}
	 */
	public function statistics(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法'
		]);
		$res = DocumentLogic::instance()->getById($params['document_id']);
		$last_history = $res->history()->orderBy('created_at', 'desc')->first();
		$data = [
			'browse_num' => $res->browse_num,
			'history_num' => $res->history->count(),
			'last_update_time' => $last_history->created_at ?? ''
		];
		return $this->data($data);
	}
}
