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
use W7\Core\Helper\Traiter\InstanceTraiter;

class ChapterApiParamLogic extends BaseLogic
{
	use InstanceTraiter;

	public function rawContentType()
	{
		return [
			'text/plain', 'application/json', 'application/javascript', 'application/xml', 'text/xml', 'text/html'
		];
	}

	public function getLocationLabel()
	{
		return [
			ChapterApiParam::LOCATION_REQUEST_QUERY_PATH => 'Path',
			ChapterApiParam::LOCATION_REQUEST_HEADER => 'Head',
			ChapterApiParam::LOCATION_REQUEST_QUERY_STRING => 'Query',
			ChapterApiParam::LOCATION_REQUEST_BODY_FROM => 'Body',
			ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED => 'Body',
			ChapterApiParam::LOCATION_REQUEST_BODY_RAW => 'Body',
			ChapterApiParam::LOCATION_REQUEST_BODY_BINARY => 'Body',
			ChapterApiParam::LOCATION_REPONSE_HEADER => 'Reponse',
			ChapterApiParam::LOCATION_REPONSE_BODY_FROM => 'Reponse',
			ChapterApiParam::LOCATION_REPONSE_BODY_URLENCODED => 'Reponse',
			ChapterApiParam::LOCATION_REPONSE_BODY_RAW => 'Reponse',
			ChapterApiParam::LOCATION_REPONSE_BODY_BINARY => 'Reponse',
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
