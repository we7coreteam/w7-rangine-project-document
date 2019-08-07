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

namespace W7\App\Controller;

use W7\Core\Controller\ControllerAbstract;
use W7\Http\Message\File\File;
use W7\Http\Message\Server\Request;

class HomeController extends ControllerAbstract
{
	public function index(Request $request)
	{
		print_r($request->post());
		return [
			'data' => [
				'uid' => 1,
				'username' => 'rangine'
			]
		];
		return 'helloWorld';
	}

	public function userLogin(Request $request, $uid = 0)
	{
		return 'user-login ---- uid : ' . $uid . ' --- post: ' . $request->post('password');
	}

	public function download()
	{
		return $this->response()->withFile(new File('/home/data/1.txt'));
	}

	public function userheader()
	{
		return $this->response()->withHeader('Content-Type', 'text/html;charset=utf-8')->withContent('withheader');
	}
}
