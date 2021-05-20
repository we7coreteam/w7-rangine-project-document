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
	 * 用户详情
	 * @param Request $request
	 * @return array
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
}
