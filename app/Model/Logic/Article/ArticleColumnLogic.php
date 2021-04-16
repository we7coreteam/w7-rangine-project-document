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

	public function info($user)
	{
		return ArticleColumn::query()->where('user_id', $user->id)->first();
	}

	public function save($user, $data)
	{
		$row = ArticleColumn::query()->where('user_id', $user->id)->first();
		if ($row) {
			$row->name = $data['name'];
			$row->save();
		} else {
			$saveData = [
				'user_id' => $user->id,
				'name' => $data['name']
			];
			$row = ArticleColumn::query()->create($saveData);
		}
		return $row;
	}
}
