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

namespace W7\App\Model\Service\Document\PostMan;

class PostManService
{
	public function documentToPostManJosn($documentId)
	{
		$service = new PostManVersion2Service();
		$data = $service->buildExportJson($documentId);
		return $data;
	}

	public function postManJsonToDocument($json)
	{
		$service = new PostManVersion2Service();
		$data = $service->importToDocument($json);
		return $data;
	}
}
