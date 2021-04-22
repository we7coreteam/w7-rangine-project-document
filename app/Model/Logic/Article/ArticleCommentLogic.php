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

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Entity\Article\ArticleComment;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleCommentLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleComment::class;

	public function store($data)
	{
		$article = Article::query()->find($data['article_id']);
		if (!$article || $article->comment_status == Article::COMMENT_STATUS_NO) {
			throw new ErrorHttpException('该文章未开启评论');
		}
		$data['status'] = ArticleComment::STATUS_YES;
		return parent::store($data);
	}
}
