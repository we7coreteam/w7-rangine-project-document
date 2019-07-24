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

namespace W7\App\Controller\Admin;

use W7\App\Model\Logic\CategoryLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Http\Message\Server\Request;

class CategoryController extends Controller
{
	public function __construct()
	{
		$this->logic = new CategoryLogic();
		$this->user = new UserLogic();
	}

	public function getCatalogue()
	{
		try {
			$res = $this->logic->getCatalogue();
			if($res){
				return $this->success($res);
			}else{
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function getDetails(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => 'ID不能为空',
			]);
			$res = $this->logic->getDetails($request->input('id'));
			if($res){
				return $this->success($res);
			}else{
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function add(Request $request)
	{
		try {
			$this->validate($request, [
				'parentId' => 'required|integer|min:0',
				'name' => 'required',
				'sort' => 'required|integer|min:0',
				'username' => 'required',
			], [
				'parentId.required' => '父级ID最小为0',
				'name.required' => '名称不能为空',
				'sort.required' => '排序最小值为0',
				'username.required' => '用户名不能为空',
			]);

			$parentId = intval($request->input('parentId'));
			$name = $request->input('name');
			$sort = $request->input('sort');
			$username = $request->input('username');

			$user = $this->user->getUser(['username'=>$username]);

			$data = [];
			$data['parent_id'] = $parentId;
			$data['name'] = $name;
			$data['sort'] = $sort;
			$data['creator_id'] = $user['id'];

			if ($parentId == 0){
//				顶级目录
				$data['levels'] = 1;
			}else{
				$categories = $this->logic->getDetails(['id'=>$parentId]);
				if($categories){
					$data['levels'] = $categories['levels'] + 1;
				}else{
					return $this->error('找不到上一级目录');
				}
			}

			$res = $this->logic->add($data);
			if($res){
				return $this->success($res);
			}else{
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function update()
	{
	}

	public function del()
	{
	}
}
