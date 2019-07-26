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
use W7\Http\Message\Server\Request;

class DocumentController extends Controller
{
	public function __construct()
	{
		$this->logic = new DocumentLogic();
	}

	public function getlist()
	{
		
	}

	public function getdetails(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => '文档ID不能为空',
			]);
			$res = $this->logic->getdetails($request->input('id'));
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
