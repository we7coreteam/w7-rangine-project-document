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

namespace W7\App\Model\Logic\Article;

use W7\App\Model\Entity\Article\ArticleTagConfig;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleTagConfigLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleTagConfig::class;

	public function index($condition = [], $page = 1, $limit = 10)
	{
		return $this->lists($condition, $page, $limit);
	}
}
