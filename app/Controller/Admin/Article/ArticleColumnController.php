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
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\Article\ArticleColumnLogic;
use W7\Http\Message\Server\Request;

class ArticleColumnController extends BaseController
{
	protected function block()
	{
		return new ArticleColumnLogic();
	}

	protected $query = [
		'like' => ['name']
	];

	/**
	 * @api {get} /admin/article/articleColumn 栏目-列表
	 * @apiName index
	 * @apiGroup articleColumnAdmin
	 *
	 * @apiSuccess {String} name 栏目名称
	 * @apiSuccess {Number} article_num 排序
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} subscribe_num 关注数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"user_id":2,"name":"栏目3","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618906453","updated_at":"1618907138"},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		$result = $this->block()->lists($condition, $page, $pageSize);
		return $this->data($result);
	}

	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id);
		return $this->data($result);
	}

	/**
	 * @api {post} /admin/article/articleColumn 栏目-新增
	 * @apiName store
	 * @apiGroup articleColumnAdmin
	 *
	 * @apiParam {String} name 栏目名称
	 * @apiParam {Number} article_num 排序
	 * @apiParam {Number} read_num 阅读数量
	 * @apiParam {Number} subscribe_num 关注数量
	 * @apiParam {Number} praise_num 点赞数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"user_id":2,"name":"栏目3","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618906453","updated_at":"1618907138"},"message":"ok"}
	 */
	public function store(Request $request)
	{
		$data = $this->validate($request, [
			'name' => 'required|string',
		], [
			'name' => '专栏名称',
		]);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->add($data);
		return $this->data($result);
	}

	/**
	 * @api {put} /admin/article/articleColumn 栏目-修改
	 * @apiName store
	 * @apiGroup articleColumnAdmin
	 *
	 * @apiParam {String} name 栏目名称
	 * @apiParam {Number} article_num 排序
	 * @apiParam {Number} read_num 阅读数量
	 * @apiParam {Number} subscribe_num 关注数量
	 * @apiParam {Number} praise_num 点赞数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"user_id":2,"name":"栏目3","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618906453","updated_at":"1618907138"},"message":"ok"}
	 */
	public function update(Request $request, $id)
	{
		$data = $this->validate($request, [
			'name' => 'required|string',
		], [
			'name' => '专栏名称',
		]);

		$user = $request->getAttribute('user');
		//必须本用户修改
		$checkData['user_id'] = $user->id;

		$result = $this->block()->update($id, $data, $checkData);
		return $this->data($result);
	}
}
