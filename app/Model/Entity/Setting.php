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

class Setting extends BaseModel
{
	public $timestamps = false;
	protected $table = 'setting';
	protected $primaryKey = 'key';

	const ERROR_NO_POWER=446;
	const ERROR_NO_LOGIN=444;

	public function getSettingAttribute() {
		if (empty($this->value)) {
			return [];
		}
		return json_decode($this->value, true);
	}
}
