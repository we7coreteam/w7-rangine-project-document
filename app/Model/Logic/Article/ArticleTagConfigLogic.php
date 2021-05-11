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
use W7\App\Model\Entity\Article\ArticleTagConfig;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleTagConfigLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleTagConfig::class;

	public function store($data)
	{
		$this->checkSave($data);
		return parent::store($data);
	}

	public function checkSave($data, $id = '')
	{
		$has = ArticleTagConfig::query()->where('name', $data['name']);
		if ($id) {
			$has->where('id', '<>', $id);
		}
		if ($has->first()) {
			throw new ErrorHttpException('标签名称已存在');
		}
		return $data;
	}

	public function update($id, $data, $checkData = [])
	{
		$this->checkSave($data, $id);
		return parent::update($id, $data, $checkData);
	}
}
