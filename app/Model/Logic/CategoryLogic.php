<?php


namespace W7\App\Model\Logic;


use W7\App\Model\Entity\Category;

class CategoryLogic extends BaseLogic
{
	public function getDetails($data)
	{
		if (isset($data['id'])){
			return Category::find($data['id']);
		}

		if (isset($data['parent_id'])){
			return Category::where('parent_id', $data['parent_id'])->first();
		}
	}

	public function getCatalogue()
	{
		return Category::where('parent_id',0)
				->orderBy('sort', 'desc')
				->get();
	}

	public function add($data)
	{
		return Category::create($data);
	}
}