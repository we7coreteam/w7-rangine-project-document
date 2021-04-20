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
use W7\App\Model\Logic\Article\ArticleColumnLogic;
use W7\Http\Message\Server\Request;

class ArticleColumnController extends BaseController
{
	protected function block()
	{
		return new ArticleColumnLogic();
	}

	/**
	 * @api {get} /articleColumn/info 文章栏目-详情
	 * @apiName info
	 * @apiGroup articleColumn
	 */
	public function info(Request $request)
	{
		$user = $request->getAttribute('user');
		$result = $this->block()->info($user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /articleColumn 文章栏目-编辑、新增
	 * @apiName info
	 * @apiGroup articleColumn
	 */
	public function store(Request $request)
	{
		$data = $this->validate($request, [
			'name' => 'required|string',
		], [
			'name.required' => '专栏名称不能为空',
		]);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	public function update(Request $request, $id)
	{
		$data = $this->validate($request, [
			'id' => 'required|integer',
			'name' => 'required|string',
		], [
			'name.required' => '专栏名称不能为空',
		]);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->update($id, $data, true);
		return $this->data($result);
	}
}
