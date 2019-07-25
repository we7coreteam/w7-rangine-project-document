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

namespace W7\App\Model\Entity;

class Chapter extends BaseModel
{
	const SHOW = 1;
	//protected $appends=['category_name', 'creator_name', 'publish_text'];


	public function getCreatedAtAttribute($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function getUpdatedAtAttribute($value)
	{
		return date('Y-m-d H:i:s', $value);
	}
}
