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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use W7\App\Controller\BaseController;
use W7\App\Model\Entity\User;
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
	 * @apiSuccess {Object} statusInfo 动态详情
	 *
	 *@apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"current_page":1,"data":[{"id":28,"user_id":201,"operator_id":201,"type":5,"relation":"User","relation_id":191,"is_show":1,"remark":"马一帆关注了用户111","created_at":"1621565275","updated_at":"1621565275","statusInfo":[{"id":191,"username":"111","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1607654193","updated_at":"1607654412","follower_num":1,"following_num":0,"article_num":0}],"time_str":"17分钟前"},{"id":25,"user_id":201,"operator_id":201,"type":3,"relation":"ArticleColumn","relation_id":41,"is_show":1,"remark":"马一帆创建了专栏123","created_at":"1621564718","updated_at":"1621564718","statusInfo":[{"id":41,"user_id":201,"name":"123","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"status":0,"created_at":"1621564718","updated_at":"1621564718","user":{"id":201,"username":"马一帆","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1621305112","updated_at":"1621305112","follower_num":3,"following_num":6,"article_num":1}}],"time_str":"26分钟前"}],"first_page_url":"\/?page=1","from":1,"last_page":1,"last_page_url":"\/?page=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":2,"total":2},"message":"ok"}
	 */
	public function index(Request $request)
	{
//	    $re = DB::select('SELECT * FROM ims_user WHERE username IN (SELECT username FROM ims_user GROUP BY username HAVING COUNT(username)> 1)');
        $users = User::whereIn('username', function ($query) {
            $query->select('username')->from('user')->groupBy('username')->havingRaw('COUNT(username) > 1');
        })->get();
        $users->map(function ($item) {
            $item->username = $item->username . Str::random(6);
            $item->save();
        });
	    return $this->data($users);
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
