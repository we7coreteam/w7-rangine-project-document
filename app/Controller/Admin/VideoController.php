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
use W7\App\Model\Logic\VideoLogic;
use W7\Http\Message\Server\Request;

class VideoController extends BaseController
{
	protected function block()
	{
		return new VideoLogic();
	}

	/**
	 * @api {get} /admin/video 视频后台-视频列表
	 * @apiName index
	 * @apiGroup adminVideo
	 *
	 * @apiParam {Number} status 审核状态0待审核1通过2拒绝
	 *
	 * @apiSuccess {String} title 视频标题
	 * @apiSuccess {String} created_at 发布时间
	 * @apiSuccess {Object} user 作者信息
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":1,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_id":1,"user_id":0,"play_num":0,"praise_num":0,"is_reprint":0,"status":1,"reason":"","created_at":"1624504193","updated_at":"1624860268","user":null}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = [];
		if (is_numeric($request->query('status', ''))) {
			$condition[] = ['status', '=', $request->query('status')];
		}
		$result = $this->block()->index($condition, $page, $pageSize, ['user'], 'updated_at desc');
		return $this->data($result);
	}

	/**
	 * @api {put} /admin/video/success/:id 视频后台-视频审核通过
	 * @apiName success
	 * @apiGroup adminVideo
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_id":1,"user_id":1,"play_num":0,"praise_num":0,"is_reprint":0,"status":1,"reason":"","created_at":"1624510479","updated_at":"1624860325"},"message":"ok"}
	 */
	public function success(Request $request, $id)
	{
		$result = $this->block()->success($id);
		return $this->data($result);
	}

	/**
	 * @api {put} /admin/video/reject/:id 视频后台-视频审核拒绝
	 * @apiName reject
	 * @apiGroup adminVideo
	 *
	 * @apiParam {String} reason 驳回原因
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_id":1,"user_id":1,"play_num":0,"praise_num":0,"is_reprint":0,"status":2,"reason":"test","created_at":"1624510479","updated_at":"1624860325"},"message":"ok"}
	 */
	public function reject(Request $request, $id)
	{
		$data = $this->validate($request, [
			'reason' => 'required|string',
		], [
			'reason' => '驳回原因',
		]);
		$result = $this->block()->reject($id, $data['reason']);
		return $this->data($result);
	}
}
