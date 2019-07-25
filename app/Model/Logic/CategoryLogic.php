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

namespace W7\App\Model\Logic;

use W7\App\Event\ChangeCategoryEvent;
use W7\App\Model\Entity\Category;

class CategoryLogic extends BaseLogic
{
	use HasRelationships;

	public function getlist()
	{
		$obj = new HasRelationships();
		return $obj;
		return Category::hasMany(self::class,'parent_id','id')
			->with(['parent_id']);
	}
	public function getDetails($data)
	{
		$res = Category::find($data['id']);

		if ($res) {
			$next = Category::where('parent_id', $res['id'])
				->orderBy('sort', 'desc')
				->get();
			$res['next'] = $next;
		}

		return $res;
	}

	public function getCatalogue()
	{
		return Category::where('parent_id', 0)
				->orderBy('sort', 'desc')
				->get();
	}

	public function add($data)
	{
		$res = Category::create($data);
		if ($res) {
			ChangeCategoryEvent::instance()->dispatch();
		}
		return $res;
	}

	public function update($id, $data)
	{
		$res = Category::where('id', $id)->update($data);
		if ($res) {
			ChangeCategoryEvent::instance()->dispatch();
		}
		return $res;
	}

	public function next($id)
	{
		$res = Category::find($id);
		if ($res) {
			return Category::where('parent_id', $res['id'])->first();
		}
		return false;
	}

	public function del($id)
	{
		$res = Category::destroy($id);
		if ($res) {
			ChangeCategoryEvent::instance()->dispatch();
		}
		return $res;
	}
}
