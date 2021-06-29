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
use W7\App\Model\Logic\Video\ActivityLogic;
use W7\Http\Message\Server\Request;

class ActivityController extends BaseController
{
	protected function block()
	{
		return new ActivityLogic();
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
	 * @api {get} /admin/video/activity 视频后台-活动列表
	 * @apiName index
	 * @apiGroup videoActivityAdmin
	 *
	 * @apiSuccess {String} name 名称
	 * @apiSuccess {String} url 链接
	 * @apiSuccess {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":1,"name":"test1","url":"222","image":"333","created_at":"1624952972","updated_at":"1624952972"}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$result = $this->block()->index([], $page, $pageSize);
		return $this->data($result);
	}

	/**
	 * @api {get} /admin/video/activity/:id 视频后台-活动详情
	 * @apiName show
	 * @apiGroup videoActivityAdmin
	 *
	 * @apiSuccess {String} name 名称
	 * @apiSuccess {String} url 链接
	 * @apiSuccess {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"name":"test1","url":"222","image":"333","created_at":"1624952972","updated_at":"1624952972"},"message":"ok"}
	 */
	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id);
		return $this->data($result);
	}

	/**
	 * @api {post} /admin/video/activity 视频后台-新增活动
	 * @apiName store
	 * @apiGroup videoActivityAdmin
	 *
	 * @apiParam {String} name 名称
	 * @apiParam {String} url 链接
	 * @apiParam {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"name":"test1","url":"222","image":"333","created_at":"1624952972","updated_at":"1624952972"},"message":"ok"}
	 */
	public function store(Request $request)
	{
		$data = $this->handleValidate($request);
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	/**
	 * @api {put} /admin/video/activity/:id 视频后台-修改活动
	 * @apiName update
	 * @apiGroup videoActivityAdmin
	 *
	 * @apiParam {String} name 名称
	 * @apiParam {String} url 链接
	 * @apiParam {String} image 图片
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"name":"test1","url":"222","image":"333","created_at":"1624952972","updated_at":"1624952972"},"message":"ok"}
	 */
	public function update(Request $request, $id)
	{
		$data = $this->handleValidate($request);
		$result = $this->block()->update($id, $data);
		return $this->data($result);
	}

	/**
	 * @api {delete} /admin/video/activity/:id 视频后台-删除活动
	 * @apiName delete
	 * @apiGroup videoActivityAdmin
	 */
	public function delete(Request $request, $id)
	{
		$result = $this->block()->destroy($id);
		return $this->data($result);
	}
}
