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

class ArticleColumnSub extends BaseModel
{
	protected $table = 'article_column';
	protected $fillable = [
		'column_id', 'user_id', 'creater_id', 'status', 'sub_time'
	];
}
