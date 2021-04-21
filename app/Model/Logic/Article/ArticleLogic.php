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
use W7\App\Model\Entity\Article\ArticleTagConfig;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = Article::class;

	public function read($id, $num = 1)
	{
		$row = Article::query()->find($id);
		$row->increment('read_num', $num);
		(new ArticleColumnLogic())->incrementNum($row->column_id, 'read_num', $num);
		return $row;
	}

	public function store($data)
	{
		$data = $this->checkPost($data);
		$row = parent::store($data);
		(new ArticleColumnLogic())->retry($row->column_id);
		return $row;
	}

	public function checkPost($data)
	{
		$column = ArticleColumn::query()->find($data['column_id']);
		if (!$column || $column->user_id != $data['user_id']) {
			throw new ErrorHttpException('专栏不存在');
		}
		if (!empty($data['tag_ids'])) {
			$tags = ArticleTagConfig::query()->whereIn('id', $data['tag_ids'])->get()->toArray();
			if (!$tags) {
				throw new ErrorHttpException('标签错误');
			}
			$tagIds = array_column($tags, 'id');
			$data['tag_ids'] = $tagIds;
		}
		return $data;
	}

	public function update($id, $data, $checkData = [])
	{
		$data = $this->checkPost($data);
		$row = parent::update($id, $data, $checkData);
		(new ArticleColumnLogic())->retry($row->column_id);
		return $row;
	}
}
