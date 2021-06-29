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

namespace W7\App\Controller\Video;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Video\CategoryConfigLogic;
use W7\Http\Message\Server\Request;

class CategoryConfigController extends BaseController
{
	protected function block()
	{
		return new CategoryConfigLogic();
	}

	/**
	 * @api {get} /video/categoryConfig 视频分类-分类列表
	 * @apiName index
	 * @apiGroup videoCategoryConfig
	 *
	 * @apiSuccess {String} name 分类名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":3,"name":"test3","created_at":"1624944838","updated_at":"1624944838"},{"id":2,"name":"test2","created_at":"1624938567","updated_at":"1624938567"},{"id":1,"name":"test1","created_at":"1624938553","updated_at":"1624938553"}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":3,"total":3},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$result = $this->block()->index([], $page, $pageSize, '', 'id desc');
		return $this->data($result);
	}
}
