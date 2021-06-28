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

namespace W7\App\Controller\Admin\Video;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Video\CategoryLogic;
use W7\Http\Message\Server\Request;

class CategoryController extends BaseController
{
	protected function block()
	{
		return new CategoryLogic();
	}

	public function handleValidate(Request $request)
	{
		return $this->validate($request, [
			'name' => 'required|string',
		], [], [
			'name' => '分类名称',
		]);
	}

	/**
	 * @api {get} /admin/video/category 视频后台-分类列表
	 * @apiName index
	 * @apiGroup videoCategory
	 *
	 * @apiSuccess {String} name 分类名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"name":"test","updated_at":"1624510146","created_at":"1624510146","id":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$result = $this->block()->index([], $page, $pageSize);
		return $this->data($result);
	}

	/**
	 * @api {post} /admin/video/category 视频后台-新增分类
	 * @apiName store
	 * @apiGroup videoCategory
	 *
	 * @apiParam {String} name 分类名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"name":"test","updated_at":"1624510146","created_at":"1624510146","id":1},"message":"ok"}
	 */
	public function store(Request $request)
	{
		$data = $this->handleValidate($request);
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	/**
	 * @api {put} /admin/video/category/:id 视频后台-修改分类
	 * @apiName update
	 * @apiGroup videoCategory
	 *
	 * @apiParam {String} name 分类名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"name":"test","updated_at":"1624510146","created_at":"1624510146","id":1},"message":"ok"}
	 */
	public function update(Request $request, $id)
	{
		$data = $this->handleValidate($request);
		$result = $this->block()->update($id, $data);
		return $this->data($result);
	}
}
