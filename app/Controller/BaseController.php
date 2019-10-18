<?php
/**
 * @author donknap
 * @date 19-10-10 下午2:07
 */

namespace W7\App\Controller;


use W7\Core\Controller\ControllerAbstract;

class BaseController extends ControllerAbstract
{
	public function data($data = [], $message = 'ok', $code = 200)
	{
		return [
			'status' => true,
			'code' => $code,
			'data' => $data,
			'message' => $message,
		];
	}
}