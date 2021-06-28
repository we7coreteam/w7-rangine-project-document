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
use W7\App\Model\Logic\Video\CarouselLogic;
use W7\Http\Message\Server\Request;

class CarouselController extends BaseController
{
	protected function block()
	{
		return new CarouselLogic();
	}

	public function handleValidate(Request $request)
	{
		return $this->validate($request, [
			'name' => 'required|string',
			'url' => 'required|string',
			'image' => 'required|string',
		], [], [
			'name' => '名称',
			'url' => '链接',
			'image' => '图片',
		]);
	}

	/**
	 * @api {get} /admin/video/carousel 视频后台-轮播列表
	 * @apiName index
	 * @apiGroup videoCarousel
	 *
	 * @apiSuccess {String} name 名称
	 * @apiSuccess {String} url 链接
	 * @apiSuccess {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$result = $this->block()->index([], $page, $pageSize);
		return $this->data($result);
	}

	/**
	 * @api {get} /admin/video/carousel/:id 视频后台-轮播详情
	 * @apiName show
	 * @apiGroup videoCarousel
	 *
	 * @apiSuccess {String} name 名称
	 * @apiSuccess {String} url 链接
	 * @apiSuccess {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *
	 */
	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id);
		return $this->data($result);
	}

	/**
	 * @api {post} /admin/video/carousel 视频后台-新增轮播
	 * @apiName store
	 * @apiGroup videoCarousel
	 *
	 * @apiParam {String} name 名称
	 * @apiParam {String} url 链接
	 * @apiParam {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *
	 */
	public function store(Request $request)
	{
		$data = $this->handleValidate($request);
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	/**
	 * @api {put} /admin/video/carousel/:id 视频后台-修改轮播
	 * @apiName update
	 * @apiGroup videoCarousel
	 *
	 * @apiParam {String} name 名称
	 * @apiParam {String} url 链接
	 * @apiParam {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *
	 */
	public function update(Request $request, $id)
	{
		$data = $this->handleValidate($request);
		$result = $this->block()->update($id, $data);
		return $this->data($result);
	}

	/**
	 * @api {delete} /admin/video/carousel/:id 视频后台-删除轮播
	 * @apiName delete
	 * @apiGroup videoCarousel
	 */
	public function delete(Request $request, $id)
	{
		$result = $this->block()->destroy($id);
		return $this->data($result);
	}
}
