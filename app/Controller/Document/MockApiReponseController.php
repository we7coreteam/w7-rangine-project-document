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

namespace W7\App\Controller\Document;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Document\MockApi\MockApiReponseLogic;
use W7\Http\Message\Server\Request;

class MockApiReponseController extends BaseController
{
	public function index(Request $request, $id, $router)
	{
		$mockApiReponseLogic = new MockApiReponseLogic();
		$ret = $mockApiReponseLogic->mackMockApiReponse($request, $id, $router);
		return $this->response()->json($ret);
	}
}
