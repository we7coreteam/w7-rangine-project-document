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

class Video extends BaseModel
{
	protected $table = 'video';
	protected $fillable = ['title', 'cover', 'url', 'description', 'time_length', 'category_id', 'user_id', 'is_reprint'];

	const STATUS_CREATE = 0;
	const STATUS_SUCCESS = 1;
	const STATUS_FAIL = 2;

	const IS_REPRINT_NO = 0;//非转载
	const IS_REPRINT_YES = 1;//转载

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
