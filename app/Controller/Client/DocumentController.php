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

namespace W7\App\Controller\Client;

use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends Controller
{
	public function __construct()
	{
		$this->logic = new DocumentLogic();
		$this->user = new UserLogic();
	}

	public function create(Request $request)
	{
		try {
			$this->validate($request, [
				'name' => 'required',
				'username' => 'required',
			], [
				'name.required' => '名称不能为空',
				'username.required' => '用户名不能为空',
			]);

			$name = $request->input('name');
			$username = $request->input('username');

			$user = $this->user->getUser(['username'=>$username]);

			$data = [];
			$data['name'] = $name;
			$data['creator_id'] = $user['id'];
			if ($request->input('description')){
				$data['description'] = $request->input('description');
			}else{
				$data['description'] = '';
			}

			$res = $this->logic->create($data);
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function update(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
				'username' => 'required',
			], [
				'id.required' => '文档ID不能为空',
				'username.required' => '用户名不能为空',
			]);
			$username = $request->input('username');
			$documentId = $request->input('id');

			$relation = $this->relation($username, $documentId);
			if ($relation !== true) {
				return $this->error($relation);
			}

			$data = [];
			if ($request->input('name')) {
				$data['name'] = $request->input('name');
			}
			if ($request->input('description')) {
				$data['description'] = $request->input('description');
			}
			if ($request->input('url')) {
				$data['url'] = $request->input('url');
			}

			$res = $this->logic->update($documentId, $data);
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function del(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
				'username' => 'required',
			], [
				'id.required' => '文档ID不能为空',
				'username.required' => '用户名不能为空',
			]);
			$relation = $this->relation($request->input('username'), $request->input('id'));
			if ($relation !== true) {
				return $this->error($relation);
			}

			$res = $this->logic->del($request->input('id'));
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	private function relation($username, $documentId)
	{
		try {
			$user = $this->user->getUser(['username'=>$username]);
			$document = $this->logic->getdetails($documentId);
			if (!$user) {
				return '用户不存在';
			}
			if (!$document) {
				return '文档不存在';
			}
			if ($user['id'] != $document['creator_id']) {
				return '只有文档创建者才可以操作';
			}
			return true;
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
