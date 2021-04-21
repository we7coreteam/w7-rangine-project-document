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
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleColumnLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleColumn::class;

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

	public function info($userId)
	{
		return ArticleColumn::query()->where('user_id', $userId)->orderBy('id')->first();
	}

	public function add($data)
	{
		$row = ArticleColumn::query()->where('user_id', $data['user_id'])->first();
		if ($row) {
			throw new ErrorHttpException('一个人只能新建一个栏目');
		} else {
			$saveData = [
				'user_id' => $data['user_id'],
				'name' => $data['name']
			];
			$row = ArticleColumn::query()->create($saveData);
		}
		return $row;
	}

	public function save($id, $data, $checkAuth = false)
	{
		$row = ArticleColumn::query()->find($id);
		if ($row) {
			if ($checkAuth && $data['user_id'] != $row->user_id) {
				throw new ErrorHttpException('栏目不存在');
			}
			$row->name = $data['name'];
			$row->save();
		}
		throw new ErrorHttpException('栏目不存在');
	}
}
