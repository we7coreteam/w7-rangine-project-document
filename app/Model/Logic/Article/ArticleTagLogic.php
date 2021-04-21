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

use W7\App\Model\Entity\Article\ArticleTag;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleTagLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleTag::class;

	public function saveTag($article)
	{
		$old = ArticleTag::query()->where('article_id', $article->id)->get()->toArray();
		$oldTagIds = array_column($old, 'tag_id');
		$insert = [];
		$update = [];
		$tagIds = $article->tag_ids;
		if ($tagIds) {
			foreach ($tagIds as $key => $val) {
				if (in_array($val, $oldTagIds)) {
					$update[$val] = [
						'tag_id' => $val,
						'article_id' => $article->id
					];
				} else {
					$insert[] = [
						'tag_id' => $val,
						'article_id' => $article->id
					];
				}
			}
		}
		if ($insert) {
			ArticleTag::query()->insert($insert);
		}
		// 删除已有的
		$removeTagIds = array_diff($oldTagIds, array_keys($update));

		if ($removeTagIds) {
			ArticleTag::query()->where('article_id', $article->id)->whereIn('tag_id', $removeTagIds)->delete();
		}

		return true;
	}
}
