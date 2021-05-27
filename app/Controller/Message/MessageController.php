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
	 * @apiSuccess {Object} target_info 消息关联内容详情
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":59,"from_id":0,"to_id":1,"text_id":59,"type":"remind","target_type":"remind_article","target_id":64,"target_url":"","is_read":2,"created_at":"1621828356","updated_at":"1621828356","deleted_at":null,"type_text":"系统通知","target_type_text":"文章通知","target_info":{"id":64,"column_id":1,"tag_ids":["15"],"user_id":1,"title":"ewfwegf","content":"<p>wergregb<\/p>","comment_status":1,"is_reprint":0,"reprint_url":"","home_thumbnail":1,"read_num":0,"praise_num":0,"collection_num":0,"status":2,"reason":"rthger","created_at":"1621818746","updated_at":"1621828356","time_str":"4小时前","status_text":"审核失败"},"time_str":"1小时前","text":{"id":59,"title":"","content":"抱歉，您发表的文章<span class='article_title'>《ewfwegf》<\/span>审核不通过，拒绝原因：rthger","created_at":"1621828356","updated_at":"1621828356"}}],"first_page_url":"\/?=1","from":1,"last_page":6,"last_page_url":"\/?=6","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":6},"message":"ok"}
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
