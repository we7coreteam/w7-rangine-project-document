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

class PostManLogic
{
	public function documentToPostManJosn($documentId)
	{
		$service = new PostManVersion2Logic();
		$data = $service->buildExportJson($documentId);
		return $data;
	}

	public function postManJsonToDocument($userId, $json)
	{
		$service = new PostManVersion2Logic();
		$data = $service->importToDocument($userId, $json);
		return $data;
	}
}
