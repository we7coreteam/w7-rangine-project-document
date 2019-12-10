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

/**
 * Class User
 * @package W7\App\Model\Entity
 *
 * @property $isFounder
 * @property $isManager
 * @property $isOperator
 * @property $isReader
 */
class User extends BaseModel
{
	const GROUP_ADMIN = 1;

	protected $table = 'user';
	protected $perPage = '10';

	public function document()
	{
		return $this->belongsTo(Document::class);
	}

	public function getIsFounderAttribute()
	{
		return $this->group_id == self::GROUP_ADMIN;
	}
}
