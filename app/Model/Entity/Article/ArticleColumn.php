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

namespace W7\App\Model\Entity\Article;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\User;

class ArticleColumn extends BaseModel
{
	protected $table = 'article_column';
	protected $fillable = [
		'user_id', 'name', 'article_num', 'read_num', 'subscribe_num', 'praise_num', 'status'
	];

	const STATUS_CREATE = 0;
	const STATUS_EDIT = 1;

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
