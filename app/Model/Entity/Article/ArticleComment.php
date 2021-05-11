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

class ArticleComment extends BaseModel
{
	protected $table = 'article_comment';
	protected $fillable = [
		'article_id', 'user_id', 'comment', 'status'
	];

	const STATUS_YES = 1;
	const STATUS_NO = 0;

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
