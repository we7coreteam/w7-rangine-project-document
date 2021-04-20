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

use W7\App\Model\Entity\Article\ArticleColumn;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleColumnLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleColumn::class;

	public function info($userId)
	{
		return ArticleColumn::query()->where('user_id', $userId)->orderBy('id')->first();
	}

	public function store($data)
	{
		$row = ArticleColumn::query()->where('user_id', $data['user_id'])->first();
		if ($row) {
			throw new \RuntimeException('一个人只能新建一个栏目');
		} else {
			$saveData = [
				'user_id' => $data['user_id'],
				'name' => $data['name']
			];
			$row = ArticleColumn::query()->create($saveData);
		}
		return $row;
	}

	public function update($data, $id, $checkAuth = false)
	{
		$row = ArticleColumn::query()->find($id);
		if ($row) {
			if ($checkAuth && $data['user_id'] != $row->user_id) {
				throw new \RuntimeException('栏目不存在');
			}
			$row->name = $data['name'];
			$row->save();
		}
		throw new \RuntimeException('栏目不存在');
	}
}
