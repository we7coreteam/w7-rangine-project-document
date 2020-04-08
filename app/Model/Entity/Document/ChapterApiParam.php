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

namespace W7\App\Model\Entity\Document;

use W7\App\Model\Entity\BaseModel;

class ChapterApiParam extends BaseModel
{
	protected $table = 'document_chapter_api_param';

	const TABLE_NAME_LENGTH = 20;//参数名称默认宽度
	const TABLE_TYPE_LENGTH = 8;//类型默认宽度
	const TABLE_ENABLED_LENGTH = 5;//类型必填宽度
	const TABLE_DESCRIPTION_LENGTH = 20;//描述默认宽度
	const TABLE_VALUE_LENGTH = 20;//示例值默认宽度
	const TABLE_RULE_LENGTH = 20;//生成规则默认宽度

	const ENABLED_NO = 1;
	const ENABLED_YES = 2;

	const TYPE_STRING = 1;
	const TYPE_NUMBER = 2;
	const TYPE_BOOLEAN = 3;
	const TYPE_OBJECT = 4;
	const TYPE_ARRAY = 5;
	const TYPE_FUNCTION = 6;
	const TYPE_REGEXP = 7;
	const TYPE_NULL = 8;

	const LOCATION_REQUEST_HEADER = 1;
	const LOCATION_REQUEST_QUERY = 2;
	const LOCATION_REQUEST_BODY_FROM = 3;
	const LOCATION_REQUEST_BODY_URLENCODED = 4;
	const LOCATION_REQUEST_BODY_RAW = 5;
	const LOCATION_REQUEST_BODY_BINARY = 6;
	const LOCATION_REPONSE_HEADER = 7;
	const LOCATION_REPONSE_BODY_FROM = 8;
	const LOCATION_REPONSE_BODY_URLENCODED = 9;
	const LOCATION_REPONSE_BODY_RAW = 10;
	const LOCATION_REPONSE_BODY_BINARY = 11;

	public static function rawContentType()
	{
		return [
			'text/plain', 'application/json', 'application/javascript', 'application/xml', 'text/xml', 'text/html'
		];
	}

	public static function getLocationLabel()
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

	public static function getEnabledLabel()
	{
		return [
			ChapterApiParam::ENABLED_NO => 'False',
			ChapterApiParam::ENABLED_YES => 'Ture',
		];
	}

	public static function getTypeLabel()
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
