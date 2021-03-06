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

namespace W7\App\Controller\Article;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\UserStatus;
use W7\App\Model\Logic\Article\ArticleColumnSubLogic;
use W7\App\Model\Logic\UserStatusLogic;
use W7\Http\Message\Server\Request;

class ArticleColumnSubController extends BaseController
{
	protected function block()
	{
		return new ArticleColumnSubLogic();
	}

	protected $query = [
		'=' => ['status']
	];

	/**
	 * @api {get} /article/articleColumnSub 关注栏目-已关注列表
	 * @apiName index
	 * @apiGroup articleColumnSub
	 *
	 * @apiParam {Number} status 关注状态1创建人2关注
	 *
	 * @apiSuccess {Number} sub_time 关注时间
	 * @apiSuccess {Number} status 状态0未关注1创建人2已关注
	 * @apiSuccess {Object} column 专栏信息
	 * @apiSuccess {String} column.name 专栏名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":2,"column_id":2,"user_id":1,"creater_id":1,"status":1,"sub_time":1618996757,"created_at":"1618996757","updated_at":"1618996757","status_text":"创建人","column":{"id":2,"user_id":1,"name":"栏目1","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618996757","updated_at":"1618996757"}},{"id":1,"column_id":1,"user_id":15,"creater_id":2,"status":1,"sub_time":1618994404,"created_at":"1618994404","updated_at":"1618994411","status_text":"创建人","column":{"id":1,"user_id":2,"name":"11","article_num":2,"read_num":2,"subscribe_num":2,"praise_num":1,"created_at":"0","updated_at":"1618994887"}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":2,"total":2},"message":"ok"}
	 **/
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		$condition[] = ['status', '>', 0];
		$result = $this->block()->index($condition, $page, $pageSize, 'column');
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleColumnSub/info 关注栏目-获取当前栏目关注状态
	 * @apiName info
	 * @apiGroup articleColumnSub
	 *
	 * @apiParam {Number} column_id 用户ID
	 *
	 * @apiSuccess {Number} sub_time 关注时间
	 * @apiSuccess {Number} status 状态0未关注1创建人2已关注
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"column_id":2,"user_id":1,"creater_id":1,"status":1,"sub_time":1618996757,"created_at":"1618996757","updated_at":"1618996757","status_text":"创建人"},"message":"ok"}
	 */
	public function info(Request $request)
	{
		$data = $this->validate($request, [
			'column_id' => 'required|integer',
		], [
			'column_id' => '专栏ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->info($data['column_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleColumnSub/sub 关注栏目-关注
	 * @apiName sub
	 * @apiGroup articleColumnSub
	 *
	 * @apiParam {Number} column_id 用户ID
	 **/
	public function sub(Request $request)
	{
		$data = $this->validate($request, [
			'column_id' => 'required|integer',
		], [
			'column_id' => '专栏ID',
		]);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->sub($data['column_id'], $user->id);
		UserStatusLogic::instance()->createStatus($result, $user, UserStatus::SUB_COLUMN);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleColumnSub/unSub 关注栏目-取消关注
	 * @apiName unSub
	 * @apiGroup articleColumnSub
	 *
	 * @apiParam {Number} column_id 用户ID
	 **/
	public function unSub(Request $request)
	{
		$data = $this->validate($request, [
			'column_id' => 'required|integer',
		], [
			'column_id' => '专栏ID',
		]);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->unSub($data['column_id'], $user->id);
		UserStatusLogic::instance()->deleteStatus($result, $user->id, UserStatus::SUB_COLUMN);
		return $this->data($result);
	}

	/**
	 * @api {get} /article/articleColumnSub/userSub 关注栏目-获取用户订阅的栏目
	 * @apiName userSub
	 * @apiGroup articleColumnSub
	 *
	 * @apiParam {Number} column_id 用户ID
	 *
	 * @apiSuccess {Object} column 栏目详情
	 * @apiSuccess {String} column.name 栏目名称
	 * @apiSuccess {Number} column.article_num 文章数量
	 * @apiSuccess {Number} column.subscribe_num 关注者数量
	 * @apiSuccess {Number} is_sub 当前登录用户是否订阅（此字段仅在用户登录时存在）1已订阅0未订阅
	 *
	 **/
	public function getUserSub(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer',
		], [
			'user_id' => '用户id',
		]);

		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = [
			['user_id', '=', $data['user_id']],
			['status', '=', 2]
		];
		$result = $this->block()->index($condition, $page, $pageSize, 'column');
		$user = $request->session->get('user');
		if ($user) {
			$result->map(function ($itme) use ($user) {
				if ($this->block()->info($itme->column_id, $user['uid'])) {
					$itme->is_sub = 1;
				} else {
					$itme->is_sub = 0;
				}
			});
		}
		return $this->data($result);
	}
}
