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

namespace W7\App\Controller\User;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\UserStatusLogic;
use W7\Http\Message\Server\Request;

class UserStatusController extends BaseController
{
	protected function block()
	{
		return new UserStatusLogic();
	}

	/**
	 * @api {get} user/userStatus 动态-获取动态列表
	 * @apiName index
	 * @apiGroup userStatus
	 *
	 * @apiParam {Number} user_id 用户id
	 *
	 * @apiSuccess {Number} type 动态类型 1创建文档2收藏文档3创建栏目4订阅栏目5关注用户
	 * @apiSuccess {String} time_str 发布时间
	 * @apiSuccess {Object} status_info 动态详情
	 * @apiSuccess {String} status_text 动态简介
	 *
	 *@apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"current_page":1,"data":[{"id":84,"user_id":201,"operator_id":201,"type":6,"relation":"Article","relation_id":73,"is_show":1,"remark":"马一帆创建了文章123","created_at":"1621826522","updated_at":"1621826522","time_str":"23分钟前","status_text":"创建了文章","status_info":{"id":73,"column_id":41,"tag_ids":["11"],"user_id":201,"title":"123","content":"<p>123<\/p>","comment_status":0,"is_reprint":0,"reprint_url":"","home_thumbnail":0,"read_num":2,"praise_num":0,"collection_num":0,"status":2,"reason":"11","created_at":"1621826522","updated_at":"1621826770","time_str":"23分钟前","status_text":"审核失败","user":{"id":201,"username":"马一帆","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1621305112","updated_at":"1621587687","follower_num":5,"following_num":6,"article_num":5}}}],"first_page_url":"\/?page=1","from":1,"last_page":3,"last_page_url":"\/?page=3","next_page_url":"\/?page=2","path":"\/","per_page":1,"prev_page_url":null,"to":1,"total":3},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$param = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$result = $this->block()->getStatus($param['user_id'], $page, $pageSize);
		return $this->data($result);
	}
}
