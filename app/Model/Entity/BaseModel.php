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

use W7\Core\Database\ModelAbstract;

class BaseModel extends ModelAbstract
{
	public $timestamps = true;
	protected $guarded = [];
	public $dateFormat = 'U';
	protected $perPage = '10';
}
