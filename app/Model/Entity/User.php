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

class User extends BaseModel
{
	const GROUP_ADMIN = 1;

	protected $table = 'user';

	public function document()
	{
		return $this->belongsTo(Document::class);
	}

	public function getIsAdminAttribute() {
		return $this->group_id == self::GROUP_ADMIN;
	}
}
