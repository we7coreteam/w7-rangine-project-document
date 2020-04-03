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

class ChapterRecord extends BaseModel
{
	public $timestamps = false;
	protected $table = 'document_chapter_record';
	protected $primaryKey = 'chapter_id';

	const TABLE_NAME_LENGTH = 20;//参数名称默认宽度
	const TABLE_TYPE_LENGTH = 8;//类型默认宽度
	const TABLE_MUST_LENGTH = 5;//类型必填宽度
	const TABLE_DESCRIPTION_LENGTH = 20;//描述默认宽度
	const TABLE_VALUE_LENGTH = 20;//示例值默认宽度
	const TABLE_MUST_YES = 1;
	const TABLE_MUST_NO = 0;

	public static function getMustLabel()
	{
		return [
			self::TABLE_MUST_YES => 'Ture',
			self::TABLE_MUST_NO => 'False',
		];
	}
}
