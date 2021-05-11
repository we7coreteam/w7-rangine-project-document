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
use W7\App\Model\Logic\Article\ArticleTagConfigLogic;
use W7\Http\Message\Server\Request;

class ArticleTagConfigController extends BaseController
{
	protected function block()
	{
		return new ArticleTagConfigLogic();
	}

	protected $query = [
		'=' => ['status'],
		'like' => ['name']
	];

	/**
	 * @api {get} /article/articleTagConfig 标签-列表
	 * @apiName index
	 * @apiGroup articleTagConfig
	 *
	 * @apiParam {String} name 标签名称
	 * @apiParam {Number} status 状态
	 *
	 * @apiSuccess {String} name 标签名称
	 * @apiSuccess {Number} sort 排序
	 * @apiSuccess {Number} status 状态
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":1,"name":"标签1","sort":0,"status":1,"created_at":"1618903332","updated_at":"1618903332"}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":20,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		$result = $this->block()->index($condition, $page, $pageSize, '', 'sort desc,id desc');
		return $this->data($result);
	}

	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id);
		return $this->data($result);
	}
}
