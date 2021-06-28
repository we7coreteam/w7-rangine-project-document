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

namespace W7\App\Model\Entity\Video;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\User;

class Comment extends BaseModel
{
	protected $table = 'video_comment';
	protected $fillable = ['video_id', 'comment', 'user_id'];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
