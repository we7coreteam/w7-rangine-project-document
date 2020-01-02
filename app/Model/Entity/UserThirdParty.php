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

class UserThirdParty extends BaseModel
{
	public $timestamps = false;
	protected $table = 'user_third_party';
	protected $primaryKey = 'id';

	public function bindUser() {
		return $this->hasOne(User::class, 'id', 'uid');
	}
}
