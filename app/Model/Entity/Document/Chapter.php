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

class Chapter extends BaseModel
{
	protected $table = 'document_chapter';

	public function getCreatedAtAttribute($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function getUpdatedAtAttribute($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function description()
	{
		return $this->hasOne(ChapterContent::class, 'chapter_id', 'id');
	}

	public function getPrevItemAttribute() {
		$item = static::query()->where('parent_id', $this->parent_id)->where('sort', '>', $this->sort)->orderBy('sort')->first();
		return $item;
	}

	public function getNextItemAttribute() {

	}
}
