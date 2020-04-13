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

namespace W7\App\Model\Logic\Document\PostMan;

use W7\App\Model\Entity\Document\ChapterApiParam;

class PostManCommonLogic
{
	//请求列表
	public function requestBodyIds()
	{
		return [
			ChapterApiParam::LOCATION_REQUEST_BODY_FROM => 'Request.Body.form-data',
			ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED => 'Request.Body.urlencoded',
			ChapterApiParam::LOCATION_REQUEST_BODY_RAW => 'Request.Body.raw',
			ChapterApiParam::LOCATION_REQUEST_BODY_BINARY => 'Request.Body.binary',
		];
	}

	//字符串是否为JSON
	public function isJson($data = '', $assoc = false)
	{
		$data = json_decode($data, $assoc);
		if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
			return $data;
		}
		return false;
	}
}
