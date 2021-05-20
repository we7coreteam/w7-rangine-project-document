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
use W7\App\Model\Logic\BaseLogic;
use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Entity\Article\ArticleCollection;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleCollectionLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleCollection::class;

	public function info($articleId, $uid)
	{
		return ArticleCollection::query()
			->where('article_id', $articleId)
			->where('user_id', $uid)
			->where('status', ArticleCollection::STATUS_YES)
			->first();
	}

	public function collection($articleId, $uid)
	{
		// 获取操作句柄
		list($article, $collection) = $this->getArticleAndCollection($articleId, $uid);
		try {
			idb()->beginTransaction();
			if ($collection) {
				if ($collection->status == ArticleCollection::STATUS_YES) {
					throw new ErrorHttpException('您已收藏该文章');
				}
				$collection->status = ArticleCollection::STATUS_YES;
				$collection->save();
			} else {
				$saveData = [
					'article_id' => $articleId,
					'user_id' => $uid,
					'status' => ArticleCollection::STATUS_YES
				];
				$collection = ArticleCollection::query()->create($saveData);
			}
			//收藏数量+1
			$num = 1;
			$article->increment('collection_num', $num);
			idb()->commit();
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		return $collection;
	}

	public function unCollection($articleId, $uid)
	{
		// 获取操作句柄
		list($article, $collection) = $this->getArticleAndCollection($articleId, $uid);
		try {
			idb()->beginTransaction();
			if ($collection) {
				if ($collection->status == ArticleCollection::STATUS_NO) {
					throw new ErrorHttpException('您未收藏该文章');
				}
				$collection->status = ArticleCollection::STATUS_NO;
				$collection->save();
				if ($article->collection_num > 0) {
					//收藏数量-1
					$num = 1;
					$article->decrement('collection_num', $num);
				}
			}
			idb()->commit();
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		return $collection;
	}

	public function getArticleAndCollection($articleId, $uid)
	{
		$article = Article::query()->find($articleId);
		if (!$article) {
			throw new ErrorHttpException('文章不存在');
		}
		$collection = ArticleCollection::query()
			->where('article_id', $articleId)
			->where('user_id', $uid)
			->first();
		return [$article, $collection];
	}
}
