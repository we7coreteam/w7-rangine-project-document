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

use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Logic\BaseLogic;

class ChapterApiParamLogic extends BaseLogic
{
	public function rawContentType()
	{
		return [
			'text/plain', 'application/json', 'application/javascript', 'application/xml', 'text/xml', 'text/html'
		];
	}

	public function getLocationLabel()
	{
		return [
			ChapterApiParam::LOCATION_REQUEST_HEADER => 'Request.Header',
			ChapterApiParam::LOCATION_REQUEST_QUERY => 'Request.Query',
			ChapterApiParam::LOCATION_REQUEST_BODY_FROM => 'Request.Body.form-data',
			ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED => 'Request.Body.urlencoded',
			ChapterApiParam::LOCATION_REQUEST_BODY_RAW => 'Request.Body.raw',
			ChapterApiParam::LOCATION_REQUEST_BODY_BINARY => 'Request.Body.binary',
			ChapterApiParam::LOCATION_REPONSE_HEADER => 'Reponse.Header',
			ChapterApiParam::LOCATION_REPONSE_BODY_FROM => 'Reponse.Body.form-data',
			ChapterApiParam::LOCATION_REPONSE_BODY_URLENCODED => 'Reponse.Body.urlencoded',
			ChapterApiParam::LOCATION_REPONSE_BODY_RAW => 'Reponse.Body.raw',
			ChapterApiParam::LOCATION_REPONSE_BODY_BINARY => 'Reponse.Body.binary',
		];
	}

	public function getEnabledLabel()
	{
		return [
			ChapterApiParam::ENABLED_NO => 'False',
			ChapterApiParam::ENABLED_YES => 'Ture',
		];
	}

	public function getTypeLabel()
	{
		return [
			ChapterApiParam::TYPE_STRING => 'String',
			ChapterApiParam::TYPE_NUMBER => 'Number',
			ChapterApiParam::TYPE_BOOLEAN => 'Boolean',
			ChapterApiParam::TYPE_OBJECT => 'Object',
			ChapterApiParam::TYPE_ARRAY => 'Array',
			ChapterApiParam::TYPE_FUNCTION => 'Function',
			ChapterApiParam::TYPE_REGEXP => 'RegExp',
			ChapterApiParam::TYPE_NULL => 'Null',
		];
	}
}
