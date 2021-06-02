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

namespace W7\App\Controller\Admin\Article;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\UserStatus;
use W7\App\Model\Logic\Article\ArticleLogic;
use W7\App\Model\Logic\UserStatusLogic;
use W7\Http\Message\Server\Request;

class ArticleController extends BaseController
{
	protected function block()
	{
		return new ArticleLogic();
	}

	protected $query = [
		'=' => ['column_id'],
		'like' => ['title']
	];

	/**
	 * @api {get} /admin/article 管理员-全部文章-列表
	 * @apiName index
	 * @apiGroup articleAdmin
	 *
	 * @apiParam {Number} column_id 栏目名称
	 * @apiParam {String} title 文章标题
	 *
	 * @apiSuccess {Number} column_id 栏目ID
	 * @apiSuccess {Array} tag_ids 标签列表
	 * @apiSuccess {Number} user_id 用户ID
	 * @apiSuccess {String} title 文章标题
	 * @apiSuccess {String} content 文章内容
	 * @apiSuccess {Number} comment_status 是否开启评论
	 * @apiSuccess {Number} is_reprint 文章来源
	 * @apiSuccess {Number} reprint_url 来源链接
	 * @apiSuccess {Number} home_thumbnail 首页缩略图
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 * @apiSuccess {Number} status 状态0待审核1已审核2审核失败
	 * @apiSuccess {Number} reason 驳回描述
	 * @apiSuccess {Object} user 作者信息
	 * @apiSuccess {String} user.username 作者昵称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":4,"column_id":1,"tag_ids":["1","2"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618912571","updated_at":"1618912571","status_text":"待审核"}],"first_page_url":"\/?=1","from":1,"last_page":4,"last_page_url":"\/?=4","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":4},"message":"ok"}
	 **/
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		if (is_numeric($request->query('status', ''))) {
			$condition[] = ['status', '=', $request->query('status')];
		}
		$result = $this->block()->index($condition, $page, $pageSize, ['tags', 'user'], 'updated_at desc');
		return $this->data($result);
	}

	public function show(Request $request, $id)
	{
		$row = $this->block()->show($id, 'tags');
		return $this->data($row);
	}

	/**
	 * @api {post} /admin/article/success 管理员-文章-审核通过
	 * @apiName success
	 * @apiGroup articleAdmin
	 *
	 * @apiParam {Number} id 文章ID
	 **/
	public function success(Request $request)
	{
		$data = $this->validate($request, [
			'id' => 'required|integer',
		], [
			'id' => '文章ID',
		]);
		$result = $this->block()->success($data['id']);
		$re = UserStatusLogic::instance()->changeShow($result, $result->user_id, UserStatus::CREATE_ARTICLE, UserStatus::SHOW);
		return $this->data($re);
	}

	/**
	 * @api {post} /admin/article/reject 管理员-文章-审核驳回
	 * @apiName reject
	 * @apiGroup articleAdmin
	 *
	 * @apiParam {Number} id 文章ID
	 **/
	public function reject(Request $request)
	{
		$data = $this->validate($request, [
			'id' => 'required|integer',
			'reason' => 'required|string',
		], [
			'id' => '文章ID',
			'reason' => '驳回原因',
		]);
		$result = $this->block()->reject($data['id'], $data['reason']);
		return $this->data($result);
	}
}
