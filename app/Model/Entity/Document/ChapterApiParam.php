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
	public $timestamps = false;
	protected $table = 'document_chapter_api_param';
	protected $fillable = ['chapter_id', 'parent_id', 'location', 'type', 'name', 'description', 'enabled', 'default_value'];

	const TABLE_NAME_LENGTH = 20;//参数名称默认宽度
	const TABLE_TYPE_LENGTH = 8;//类型默认宽度
	const TABLE_ENABLED_LENGTH = 5;//类型必填宽度
	const TABLE_DESCRIPTION_LENGTH = 20;//描述默认宽度
	const TABLE_VALUE_LENGTH = 20;//示例值默认宽度

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
	const LOCATION_REQUEST_QUERY_STRING = 2;
	const LOCATION_REQUEST_BODY_FROM = 3;
	const LOCATION_REQUEST_BODY_URLENCODED = 4;
	const LOCATION_REQUEST_BODY_RAW = 5;
	const LOCATION_REQUEST_BODY_BINARY = 6;
	const LOCATION_REPONSE_HEADER = 7;
	const LOCATION_REPONSE_BODY_FROM = 8;
	const LOCATION_REPONSE_BODY_URLENCODED = 9;
	const LOCATION_REPONSE_BODY_RAW = 10;
	const LOCATION_REPONSE_BODY_BINARY = 11;
	const LOCATION_REQUEST_QUERY_PATH = 12;
}
