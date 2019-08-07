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

namespace W7\App\Model\Service\Api;

class TestApi extends Curl
{
	protected $baseUrl = 'www.baidu.com';

	public function getChapter($data=[])
	{
		$this->responseType = 'html';
		dd($this->get('admin/chapter/index', $data));
	}
}
