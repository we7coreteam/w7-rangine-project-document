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
	 * @api {get} /message 我的-消息列表
	 * @apiName index
	 * @apiGroup message
	 *
	 * @apiParam {String} target_type 消息类型
	 * @apiParam {Number} is_read 状态1已读2未读
	 *
	 * @apiSuccess {Object} text 消息内容对象
	 * @apiSuccess {String} text.content 消息内容
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":3,"from_id":0,"to_id":1,"text_id":3,"type":"remind","target_type":"remind_article","target_id":4,"target_url":"","is_read":2,"created_at":"1970-01-01 08:33:41","updated_at":"1970-01-01 08:33:41","deleted_at":null,"type_text":"系统通知","target_type_text":"文章通知","text":{"id":3,"title":"","content":"您的文章审核未通过，原因：111","created_at":"1970-01-01 08:33:41","updated_at":"1970-01-01 08:33:41"}}],"first_page_url":"\/?=1","from":1,"last_page":3,"last_page_url":"\/?=3","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":3},"message":"ok"}
	 **/
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$limit = $request->query('limit', 10);
		$user = $request->getAttribute('user');
		$result = $this->block()->getFrontendLists(
			$user->id,
			$page,
			$limit,
			$request->input('target_type', null),
			$request->input('is_read', null)
		);
		return $this->data($result);
	}

	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id, 'text');
		return $this->data($result);
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
		$result = $this->block()->updateIsRead($user->id, $data['ids']);
		return $this->data($result);
	}

	/**
	 * @api {post} /message/readAll 我的-消息全部标记已读
	 * @apiName readAll
	 * @apiGroup message
	 */
	public function readAll(Request $request)
	{
		$user = $request->getAttribute('user');
		$result = $this->block()->updateIsReadAll($user->id);
		return $this->data($result);
	}
}
