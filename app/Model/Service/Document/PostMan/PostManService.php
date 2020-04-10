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
	protected $documentId;
	protected $version;

	public function __construct($documentId, $version = 2)
	{
		$this->documentId = $documentId;
		$this->version = $version;
	}

	public function documentToPostMan()
	{
		if ($this->version == 2) {
			$service = new PostManVersion2Service();
		} else {
			$service = new PostManVersion1Service();
		}
		$data = $service->buildJson($this->documentId);
		return $data;
	}

	public function postManToDocument()
	{
	}
}
