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

namespace W7\App\Model\Logic\Video;

use W7\App\Model\Entity\Video\Category;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class CategoryLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = Category::class;

	public function saveCategory($video)
	{
		$old = Category::query()->where('video_id', $video->id)->get()->toArray();
		$oldCategoryIds = array_column($old, 'category_id');
		$insert = [];
		$update = [];
		$categoryIds = $video->category_ids;
		if ($categoryIds) {
			foreach ($categoryIds as $key => $val) {
				if (in_array($val, $oldCategoryIds)) {
					$update[] = $val;
				} else {
					$now = time();
					$insert[] = [
						'category_id' => $val,
						'video_id' => $video->id,
						'created_at' => $now,
						'updated_at' => $now
					];
				}
			}
		}
		if ($insert) {
			Category::query()->insert($insert);
		}
		// 删除已有的
		$removeCategoryIds = array_diff($oldCategoryIds, $update);

		if ($removeCategoryIds) {
			Category::query()->where('video_id', $video->id)->whereIn('category_id', $removeCategoryIds)->delete();
		}

		return true;
	}
}
