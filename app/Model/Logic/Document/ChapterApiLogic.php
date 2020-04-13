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

namespace W7\App\Model\Logic\Document;

use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ChapterApiLogic extends BaseLogic
{
	use InstanceTraiter;
	public function getMethodLabel()
	{
		return [
			ChapterApi::METHOD_GET => 'GET',
			ChapterApi::METHOD_POST => 'POST',
			ChapterApi::METHOD_PUT => 'PUT',
			ChapterApi::METHOD_PATCH => 'PATCH',
			ChapterApi::METHOD_DELETE => 'DELETE',
			ChapterApi::METHOD_COPY => 'COPY',
			ChapterApi::METHOD_HEAD => 'HEAD',
			ChapterApi::METHOD_PTIONS => 'PTIONS',
			ChapterApi::METHOD_LINK => 'LINK',
			ChapterApi::METHOD_UNLINK => 'UNLINK',
			ChapterApi::METHOD_PURGE => 'PURGE',
			ChapterApi::METHOD_LOCK => 'LOCK',
			ChapterApi::METHOD_UNLOCK => 'UNLOCK',
			ChapterApi::METHOD_PROPFIND => 'PROPFIND',
			ChapterApi::METHOD_VIEW => 'VIEW',
		];
	}

	//状态码
	public function getStatusCode()
	{
		return [
			200, 301, 403, 404, 500, 502, 503, 504
		];
	}
}
