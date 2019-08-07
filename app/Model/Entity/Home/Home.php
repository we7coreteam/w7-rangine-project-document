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

namespace W7\App\Model\Entity\Home;

use W7\Core\Database\ModelAbstract;

class Home extends ModelAbstract
{
	public $timestamps = false;
	protected $table = 'home';
	protected $primaryKey = 'id';
	protected $fillable = [];
}
