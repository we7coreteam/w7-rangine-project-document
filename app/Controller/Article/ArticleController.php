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

namespace W7\App\Controller\Article;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Article\ArticleLogic;
use W7\Http\Message\Server\Request;

class ArticleController extends BaseController
{
	protected function block()
	{
		return new ArticleLogic();
	}

	protected $query = [
		'=' => ['status', 'column_id'],
		'like' => ['title']
	];

	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$limit = $request->query('limit', 20);
		$condition = $this->block()->handleCondition($this->query);
		$result = $this->block()->lists($condition, $page, $limit);
		return $this->data($result);
	}
}
