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

namespace W7\App\Controller\Common;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\UserLogic;
use W7\App\Model\Logic\UserOperateLogic;
use W7\Http\Message\Server\Request;

class UserController extends BaseController
{
	protected function block()
	{
		return new UserLogic();
	}

	/**
	 * @api {post} /user/follow 用户-用户详情
	 * @apiName info
	 * @apiGroup user
	 *
	 * @apiParam {String} username 用户名
	 * @apiParam {String} avatar 头像
	 * @apiParam {String} company 公司和职称
	 * @apiParam {String} resume 个人简历
	 * @apiParam {String} address 所在城市
	 * @apiParam {Number} follower_num 关注者数量
	 * @apiParam {Number} following_num 关注了数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/AsU43YDSTlhxdSInHsEhcc0603ec6f3Y.jpg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级阿萨德阿萨德阿撒","skill":"微擎开发者，dz开发者.","address":"合肥","created_at":"1569409778","updated_at":"1621402909","follower_num":1,"following_num":2,"article_num":14},"message":"ok"}
	 */
	public function info(Request $request)
	{
		$param = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$Logic = new UserLogic();
		$user = $Logic->getByUid($param['user_id'])->setHidden(['userpass']);
		return $this->data($user);
	}

	public function update(Request $request)
	{
		$param = $this->validate($request, [
			'id' => 'required|integer',
			'avatar' => 'sometimes|required',
			'company' => 'sometimes|required',
			'resume' => 'sometimes|required',
			'skill' => 'sometimes|required',
			'address' => 'sometimes|required',
		], [
			'id' => '用户id',
			'avatar' => '头像',
			'company' => '公司和职称',
			'resume' => '简介',
			'skill' => '技能',
			'address' => '地址'
		]);
		$Logic = new UserLogic();
		$user = $Logic->updateUser($param);
		return $this->data($user);
	}

	/**
	 * 我的动态
	 */
	public function operate(Request $request)
	{
		$param = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$page = $request->input('page', 1);
		$size = $request->input('page_size', 20);
		$Logic = new UserOperateLogic();
		$param['operate'] = [UserOperateLog::CREATE,UserOperateLog::COLLECT,UserOperateLog::COLUMN_CREATE,UserOperateLog::COLUMN_SUB];
		return $this->data($Logic->lists($param, $page, $size));
	}

	/**
	 * @api {post} /user/follow 用户-关注
	 * @apiName follow
	 * @apiGroup user
	 *
	 * @apiParam {Number} user_id 关注用户id
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":[],"message":"ok"}
	 */
	public function follow(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$user = $request->getAttribute('user');
		$this->block()->follow($data['user_id'], $user);
		return $this->data();
	}

	/**
	 * @api {post} /user/unFollow 用户-取消关注
	 * @apiName unFollow
	 * @apiGroup user
	 *
	 * @apiParam {Number} user_id 取消关注用户id
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":[],"message":"ok"}
	 */
	public function unFollow(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$user = $request->getAttribute('user');
		$this->block()->unFollow($data['user_id'], $user);
		return $this->data();
	}

	/**
	 * @api {get} /user/isFollowing 用户-判断用户是否关注
	 * @apiName isFollowing
	 * @apiGroup user
	 *
	 * @apiParam {Number} user_id 用户id
	 *
	 * @apiSuccess {Boolean} is_following true已关注false未关注
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"is_following":true},"message":"ok"}
	 */
	public function isFollowing(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$user = $request->getAttribute('user');
		$re = $this->block()->isFollowing($data['user_id'], $user);
		return $this->data(['is_following' => $re]);
	}

	/**
	 * @api {get} /user/followers 用户-获取关注者用户
	 * @apiName followers
	 * @apiGroup user
	 *
	 * @apiParam {String} username 用户名
	 * @apiParam {String} avatar 头像
	 * @apiParam {String} skill 简介
	 * @apiParam {Number} article_num 文章数量
	 * @apiParam {Number} follower_num 关注者数量
	 * @apiParam {Number} is_following 是否关注此用户（登录的情况下有该属性）1已关注0未关注
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":2,"username":"rxw","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1576832671","updated_at":"1577166012","follower_num":2,"following_num":1,"article_num":0,"pivot":{"user_id":201,"follower_id":2}},{"id":3,"username":"donknap","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1576832694","updated_at":"1576832694","follower_num":0,"following_num":1,"article_num":0,"pivot":{"user_id":201,"follower_id":3}},{"id":4,"username":"jiajia123","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1577072657","updated_at":"1577072657","follower_num":1,"following_num":1,"article_num":0,"pivot":{"user_id":201,"follower_id":4}}],"first_page_url":"\/?page=1","from":1,"last_page":1,"last_page_url":"\/?page=1","next_page_url":null,"path":"\/","per_page":20,"prev_page_url":null,"to":3,"total":3},"message":"ok"}
	 */
	public function followers(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$user = $request->session->get('user');
		$re = $this->block()->getFollowers($data['user_id'], $user, $page, $pageSize);
		return $this->data($re);
	}

	/**
	 * @api {get} /user/followings 用户-获取关注了的用户
	 * @apiName followings
	 * @apiGroup user
	 *
	 * @apiParam {String} username 用户名
	 * @apiParam {String} avatar 头像
	 * @apiParam {String} skill 简介
	 * @apiParam {Number} article_num 文章数量
	 * @apiParam {Number} follower_num 关注者数量
	 * @apiParam {Number} is_following 是否关注此用户（登录的情况下有该属性）1已关注0未关注
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":2,"username":"rxw","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1576832671","updated_at":"1577166012","is_following":1,"follower_num":2,"following_num":1,"article_num":0,"pivot":{"follower_id":1,"user_id":2,"created_at":"0","updated_at":"0"}},{"id":4,"username":"jiajia123","avatar":"","remark":"","is_ban":0,"group_id":0,"company":"","resume":"","skill":"","address":"","created_at":"1577072657","updated_at":"1577072657","is_following":0,"follower_num":1,"following_num":1,"article_num":0,"pivot":{"follower_id":1,"user_id":4,"created_at":"0","updated_at":"0"}}],"first_page_url":"\/?page=1","from":1,"last_page":1,"last_page_url":"\/?page=1","next_page_url":null,"path":"\/","per_page":20,"prev_page_url":null,"to":2,"total":2},"message":"ok"}
	 */
	public function followings(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer'
		], [
			'user_id' => '用户id'
		]);
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$user = $request->session->get('user');
		$re = $this->block()->getFollowings($data['user_id'], $user, $page, $pageSize);
		return $this->data($re);
	}
}
