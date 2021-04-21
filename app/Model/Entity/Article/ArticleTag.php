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

class ArticleTag extends BaseModel
{
	protected $table = 'article_tag';
	protected $fillable = [
		'tag_id', 'article_id', 'created_at', 'updated_at'
	];

	public function tagConfig()
	{
		return $this->hasOne(ArticleTagConfig::class, 'id', 'tag_id');
	}
}
