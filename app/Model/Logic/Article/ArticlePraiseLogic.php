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
use W7\App\Model\Entity\Article\ArticlePraise;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticlePraiseLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticlePraise::class;

	public function info($articleId, $uid)
	{
		return ArticlePraise::query()
			->where('article_id', $articleId)
			->where('user_id', $uid)
			->where('status', ArticlePraise::STATUS_YES)
			->first();
	}

	public function praise($articleId, $uid)
	{
		$article = Article::query()->find($articleId);
		if (!$article) {
			throw new ErrorHttpException('文章不存在');
		}
		$num = 1;
		$row = ArticlePraise::query()
			->where('article_id', $articleId)
			->where('user_id', $uid)
			->first();
		try {
			idb()->beginTransaction();
			if ($row) {
				if ($row->status == ArticlePraise::STATUS_YES) {
					throw new ErrorHttpException('您已点赞');
				}
				$row->status = ArticlePraise::STATUS_YES;
				$row->save();
			} else {
				$saveData = [
					'article_id' => $articleId,
					'user_id' => $uid,
					'status' => ArticlePraise::STATUS_YES,
					'praise_time' => time()
				];
				$row = ArticlePraise::query()->create($saveData);
			}
			//点赞数量+1
			$article->increment('praise_num', $num);
			$articleColumn = (new ArticleColumnLogic())->incrementNum($article, 'praise_num', $num);
			idb()->commit();
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		return [
			'article_praise' => $row,
			'article' => $article,
			'article_column' => $articleColumn
		];
	}

	public function unPraise($articleId, $uid)
	{
		$article = Article::query()->find($articleId);
		if (!$article) {
			throw new ErrorHttpException('文章不存在');
		}
		$num = 1;
		$row = ArticlePraise::query()
			->where('article_id', $articleId)
			->where('user_id', $uid)
			->first();
		try {
			idb()->beginTransaction();
			if ($row) {
				if ($row->status == ArticlePraise::STATUS_NO) {
					throw new ErrorHttpException('您已取消点赞');
				}
				$row->status = ArticlePraise::STATUS_NO;
				$row->save();
				if ($article->praise_num > 0) {
					//点赞数量-1
					$article->decrement('praise_num', $num);
				}
				$articleColumn = (new ArticleColumnLogic())->decrementNum($article, 'praise_num', $num);
			} else {
				//没有记录
				$articleColumn = (new ArticleColumnLogic())->retry($article->column_id);
			}
			idb()->commit();
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		return [
			'article_praise' => $row,
			'article' => $article,
			'article_column' => $articleColumn
		];
	}
}
