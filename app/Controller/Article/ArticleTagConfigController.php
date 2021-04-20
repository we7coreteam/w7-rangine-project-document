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
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\Article\ArticleTagConfigLogic;
use W7\Http\Message\Server\Request;

class ArticleTagConfigController extends BaseController
{
	protected $query = [
		'=' => ['status'],
		'like' => ['name']
	];

	protected function block()
	{
		return new ArticleTagConfigLogic();
	}

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
	 * {"status":true,"code":200,"data":{"name":"标签3","updated_at":"1618902333","created_at":"1618902333","id":3},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$limit = $request->query('limit', 20);
		$condition = $this->block()->handleCondition($this->query);
		$result = $this->block()->lists($condition, $page, $limit);
		return $this->data($result);
	}

	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleTagConfig 标签-新增
	 * @apiName store
	 * @apiGroup articleTagConfig
	 *
	 * @apiParam {String} name 标签名称
	 * @apiParam {Number} sort 排序
	 * @apiParam {Number} status 状态
	 *
	 * @apiSuccess {String} name 标签名称
	 * @apiSuccess {Number} sort 排序
	 * @apiSuccess {Number} status 状态
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"name":"标签3","updated_at":"1618902333","created_at":"1618902333","id":3},"message":"ok"}
	 */
	public function store(Request $request)
	{
		$data = $this->validate($request, [
			'name' => 'required|string',
			'sort' => 'integer',
			'status' => 'integer',
		], [
			'name.required' => '标签名称不能为空',
			'sort' => '排序',
			'status' => '状态',
		]);
		$user = $request->getAttribute('user');
		if ($user->group_id != User::GROUP_ADMIN) {
			throw new ErrorHttpException('当前账户没有权限编辑标签');
		}
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	/**
	 * @api {put} /article/articleTagConfig/:id 标签-修改
	 * @apiName update
	 * @apiGroup articleTagConfig
	 *
	 * @apiParam {String} name 标签名称
	 * @apiParam {Number} sort 排序
	 * @apiParam {Number} status 状态
	 *
	 * @apiSuccess {String} name 标签名称
	 * @apiSuccess {Number} sort 排序
	 * @apiSuccess {Number} status 状态
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"name":"标签3","updated_at":"1618902333","created_at":"1618902333","id":3},"message":"ok"}
	 */
	public function update(Request $request, $id)
	{
		$data = $this->validate($request, [
			'name' => 'required|string',
			'sort' => 'integer',
			'status' => 'integer',
		], [
			'name.required' => '标签名称不能为空',
			'sort' => '排序',
			'status' => '状态',
		]);

		$user = $request->getAttribute('user');
		if ($user->group_id != User::GROUP_ADMIN) {
			throw new ErrorHttpException('当前账户没有权限编辑标签');
		}
		$result = $this->block()->update($id, $data);
		return $this->data($result);
	}
}
