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
use W7\App\Model\Entity\Article\ArticleColumn;
use W7\App\Model\Entity\Article\ArticleColumnSub;
use W7\App\Model\Entity\Article\ArticleTag;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleColumnLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleColumn::class;

	//重置统计
	public function retry($id)
	{
		$row = ArticleColumn::query()->find($id);
		if ($row) {
			//重算文章、点赞、阅读数量-只统计审核通过的
			$sum = Article::query()
				->selectRaw('count(1) sum,sum(read_num) as sum_read_num,sum(praise_num) as sum_praise_num')
				->where('column_id', $id)
				->where('status', Article::STATUS_SUCCESS)
				->first();
			if ($sum) {
				$row->article_num = $sum->sum;
				$row->read_num = $sum->sum_read_num;
				$row->praise_num = $sum->sum_praise_num;
				$row->save();
			} else {
				$row->article_num = 0;
				$row->read_num = 0;
				$row->praise_num = 0;
				$row->save();
			}
			return $row;
		}
		return false;
	}

	//单条增加
	public function incrementNum($article, $field, $num = 1)
	{
		//只统计审核通过的
		if ($article->status = Article::STATUS_SUCCESS) {
			$row = ArticleColumn::query()->find($article->column_id);
			if ($row) {
				$row->increment($field, $num);
				return $row;
			}
		}
		return false;
	}

	//单条减少
	public function decrementNum($article, $field, $num = 1)
	{
		try {
			//只统计审核通过的
			if ($article->status = Article::STATUS_SUCCESS) {
				$row = ArticleColumn::query()->find($article->column_id);
				if ($row) {
					$row->decrement($field, $num);
					return $row;
				}
			}
		} catch (\Exception $e) {
			//减少<0兼容
		}
		return false;
	}

	public function info($userId)
	{
		return ArticleColumn::query()->where('user_id', $userId)->with(['user'])->orderBy('id')->first();
	}

	public function tags($columnId)
	{
		return ArticleTag::query()
			->leftJoin('article', 'article.id', 'article_tag.article_id')
			->select(['article_tag.*'])
			->with('tagConfig')
			->where('article_tag.column_id', $columnId)
			->where('article.status', Article::STATUS_SUCCESS)
			->groupBy(['article_tag.tag_id'])
			->get();
	}

	public function add($data)
	{
		$row = ArticleColumn::query()->where('user_id', $data['user_id'])->first();
		if ($row) {
			throw new ErrorHttpException('一个人只能新建一个栏目');
		} else {
			try {
				idb()->beginTransaction();
				$saveData = [
					'user_id' => $data['user_id'],
					'name' => $data['name']
				];
				$row = ArticleColumn::query()->create($saveData);
				//专栏关注
				$subData = [
					'column_id' => $row->id,
					'user_id' => $row->user_id,
					'creater_id' => $row->user_id,
					'status' => ArticleColumnSub::STATUS_CREATER,
					'sub_time' => time()
				];
				ArticleColumnSub::query()->create($subData);
				idb()->commit();
			} catch (\Exception $e) {
				idb()->rollBack();
				throw new ErrorHttpException($e->getMessage());
			}
		}
		return $row;
	}

	public function save($id, $data, $checkData = [])
	{
		$row = ArticleColumn::query()->find($id);
		if ($row) {
			if ($checkData['user_id'] != $row->user_id) {
				throw new ErrorHttpException('栏目不存在');
			}
			if ($row->status != ArticleColumn::STATUS_CREATE) {
				throw new ErrorHttpException('栏目只能修改一次');
			}
			$row->status = ArticleColumn::STATUS_EDIT;
			$row->name = $data['name'];
			$row->save();
			return $row;
		}
		throw new ErrorHttpException('栏目不存在');
	}
}
