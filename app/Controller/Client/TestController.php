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

use W7\App\Model\Logic\TestLogic;
use W7\Http\Message\Server\Request;

class TestController extends Controller
{
	public function __construct()
	{
		$this->logic = new TestLogic();
	}

	public function index(Request $request)
	{
		try {
//			$request->session->set('user_id', '10086');
			return $this->success($request->session->get('user_id'));
			if (isset($_SESSION['a'])) {
				var_dump('read');
			} else {
				var_dump('write');
				$_SESSION['a'] = 'is ok';
			}
			$r = $_SESSION['a'];
			unset($_SESSION);

			return $this->success($r);
			cache()->set('test', '你好，世界 + hello world = PHP', 5);
			return $this->success(cache()->get('test'));
			//            $this->validate($request, [
			//                'name' => 'required|max:255',
			//                'id' => 'required',
			//            ],[
			//                'id.required' => 'id必填',
			//            ]);
//
			//            $name = $request->input('name');
			//            $id = $request->input('id');
			//            $res = $this->logic->addUser($name);
			$res = $this->logic->getUser(0);
			if ($res) {
				return $this->success($res);
			}
			return $this->error('用户不存在');
		} catch (\Exception $e) {
			return $this->error($e->getMessage(), 400, [$e->getFile(),$e->getLine()]);
		}
	}
}
