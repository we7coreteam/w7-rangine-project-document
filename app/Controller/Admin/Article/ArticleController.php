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
use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Logic\Article\ArticleLogic;
use W7\Http\Message\Server\Request;

class ArticleController extends BaseController
{
	protected function block()
	{
		return new ArticleLogic();
	}

	protected $query = [
		'=' => ['status', 'column_id'],
		'like' => ['title']
	];

	/**
	 * @api {get} /admin/article 全部文章-列表
	 * @apiName index
	 * @apiGroup article
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
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":4,"column_id":1,"tag_ids":["1","2"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618912571","updated_at":"1618912571","status_text":"待审核"}],"first_page_url":"\/?=1","from":1,"last_page":4,"last_page_url":"\/?=4","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":4},"message":"ok"}
	 **/

	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$limit = $request->query('limit', 20);
		$condition = $this->block()->handleCondition($this->query);
		$result = $this->block()->lists($condition, $page, $limit);
		return $this->data($result);
	}
}
