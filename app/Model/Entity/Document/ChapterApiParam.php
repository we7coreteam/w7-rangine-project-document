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
	protected $primaryKey = 'chapter_id';

	const ENABLED_NO = 1;
	const ENABLED_YES = 2;

	public static function getEnabledLabel()
	{
		return [
			self::ENABLED_NO => 'False',
			self::ENABLED_YES => 'Ture',
		];
	}
}
