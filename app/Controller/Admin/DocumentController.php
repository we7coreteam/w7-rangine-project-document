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

	public function index()
	{
	}
}
