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

namespace W7\App\Controller\Message;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Message\MessageLogic;
use W7\Http\Message\Server\Request;

class MessageController extends BaseController
{
	protected function block()
	{
		return new MessageLogic();
	}

	/**
	 * @api {get} /message/indexMy 我的-消息列表
	 * @apiName indexMy
	 * @apiGroup message
	 *
	 * @apiParam {String} target_type 消息类型
	 * @apiParam {Number} is_read 状态1已读2未读
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":4,"column_id":1,"tag_ids":["1","2"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618912571","updated_at":"1618912571","status_text":"待审核"}],"first_page_url":"\/?=1","from":1,"last_page":4,"last_page_url":"\/?=4","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":4},"message":"ok"}
	 **/
	public function indexMy(Request $request)
	{
		$page = $request->query('page', 1);
		$limit = $request->query('limit', 10);
		$user = $request->getAttribute('user');
		$this->block()->getFrontendLists(
			$user->id,
			$page,
			$limit,
			$request->input('target_type', null),
			$request->input('is_read', null)
		);
	}

	public function show(Request $request, $id)
	{
		$this->block()->show($id);
	}

	/**
	 * @api {post} /message/read 我的-消息部分标记已读
	 * @apiName read
	 * @apiGroup message
	 *
	 * @apiParam {Array} ids 标记ID列表
	 */
	public function read(Request $request)
	{
		$data = $this->validate($request, [
			'ids' => 'required|array',
		], [
			'ids' => '消息列表',
		]);
		$user = $request->getAttribute('user');
		$this->block()->updateIsRead($user->id, $data['ids']);
	}

	/**
	 * @api {post} /message/readAll 我的-消息全部标记已读
	 * @apiName readAll
	 * @apiGroup message
	 */
	public function readAll(Request $request)
	{
		$user = $request->getAttribute('user');
		$this->block()->updateIsReadAll($user->id);
	}
}
