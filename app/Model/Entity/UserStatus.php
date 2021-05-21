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

class UserStatus extends BaseModel
{
	protected $table = 'user_status';
	protected $appends = ['time_str'];

	const CREATE_DOCUMENT = 1; // 创建文档
	const COLLECT_DOCUMENT = 2; // 收藏/星标文档
	const CREATE_COLUMN = 3; // 创建栏目
	const SUB_COLUMN = 4; // 订阅栏目
	const FOLLOW_USER = 5; // 关注用户

	public function getTimeStrAttribute()
	{
		return timeToString($this->created_at->unix());
	}
}
