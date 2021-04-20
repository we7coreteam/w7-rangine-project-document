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
	protected function block()
	{
		return new ArticleTagConfigLogic();
	}

	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$limit = $request->query('limit', 20);
		$condition = $this->validate($request, [
			'name' => 'string',
			'status' => 'integer',
		], [
			'name' => '标签名称',
			'status' => '状态',
		]);
		$this->block()->lists($condition, $page, $limit);
	}

	public function show(Request $request, $id)
	{
		$this->block()->show($id);
	}

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

	public function update(Request $request, $id)
	{
		$data = $this->validate($request, [
			'id' => 'required|integer',
			'name' => 'required|string',
		], [
			'name.required' => '专栏名称不能为空',
		]);

		$user = $request->getAttribute('user');
		if ($user->group_id != User::GROUP_ADMIN) {
			throw new ErrorHttpException('当前账户没有权限编辑标签');
		}
		$result = $this->block()->update($id,$data);
		return $this->data($result);
	}
}
