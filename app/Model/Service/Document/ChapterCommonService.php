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

namespace W7\App\Model\Service\Document;

use W7\App\Model\Entity\Document\ChapterApiParam;

class ChapterCommonService
{
	public function is_assoc($arr)
	{
		//array(1, 2, 3, 4, 5, 6, 7);// 输出false
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	public function isJson($data = '', $assoc = false)
	{
		$data = json_decode($data, $assoc);
		if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
			return $data;
		}
		return false;
	}

	public function requestIds()
	{
		return [
			ChapterApiParam::LOCATION_REQUEST_HEADER => 'Request.Header',
			ChapterApiParam::LOCATION_REQUEST_QUERY => 'Request.Query',
			ChapterApiParam::LOCATION_REQUEST_BODY_FROM => 'Request.Body.form-data',
			ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED => 'Request.Body.urlencoded',
			ChapterApiParam::LOCATION_REQUEST_BODY_RAW => 'Request.Body.raw',
			ChapterApiParam::LOCATION_REQUEST_BODY_BINARY => 'Request.Body.binary',
		];
	}

	public function reponseIds()
	{
		return [
			ChapterApiParam::LOCATION_REPONSE_HEADER => 'Reponse.Header',
			ChapterApiParam::LOCATION_REPONSE_BODY_FROM => 'Reponse.Body.form-data',
			ChapterApiParam::LOCATION_REPONSE_BODY_URLENCODED => 'Reponse.Body.urlencoded',
			ChapterApiParam::LOCATION_REPONSE_BODY_RAW => 'Reponse.Body.raw',
			ChapterApiParam::LOCATION_REPONSE_BODY_BINARY => 'Reponse.Body.binary',
		];
	}
}
