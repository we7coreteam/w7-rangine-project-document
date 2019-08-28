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

use W7\Core\Controller\ControllerAbstract;

class Controller extends ControllerAbstract
{
	protected $logic = null;

	public function success($data=[], $message='ok', $code=200)
	{
		return [
			'status' => true,
			'code' => $code,
			'data' => $data,
			'message' => $message,
		];
	}

	public function error($message='fail', $code=400, $data=[])
	{
		return [
			'status' => false,
			'code' => $code,
			'data' => $data,
			'message' => $message,
		];
	}

	public function documentAuth($id, $auth)
	{
		if (APP_AUTH_ALL !== $auth && !in_array($id, $auth)) {
			return ['status' => false,'msg' => '无权操作'];
		}
		return ['status' => true];
	}
}
