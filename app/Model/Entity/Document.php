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

namespace W7\App\Model\Entity;

class Document extends BaseModel
{
	const SHOW = 1;
	protected $appends=['category_name', 'creator_name', 'publish_text'];

	public function getCategoryNameAttribute()
	{
		$category = Category::find($this->category_id);
		if (!$category) {
			return '[该分类已删除]';
		}
		$name = $category->name;

		while ($category->parent_id) {
			$category = Category::find($category->parent_id);
			if (!$category) {
				return '[该分类已删除] > '.$name;
			}
			$name = $category->name.' > '.$name;
		}

		return $name;
	}

	public function getCreatorNameAttribute()
	{
		$user = User::find($this->creator_id);
		if ($user) {
			return $user->username;
		}
		return '[该用户已被删除]';
	}

	public function getPublishTextAttribute()
	{
		if ($this->is_show == 1) {
			return '已发布';
		}
		if ($this->is_show == 0) {
			return '未发布';
		}
		return '未知状态';
	}

	public function getCreatedAtAttribute($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function getUpdatedAtAttribute($value)
	{
		return date('Y-m-d H:i:s', $value);
	}
}
