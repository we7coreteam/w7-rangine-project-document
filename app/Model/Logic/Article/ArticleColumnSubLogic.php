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
use W7\App\Model\Entity\Article\ArticleColumn;
use W7\App\Model\Entity\Article\ArticleColumnSub;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleColumnSubLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = ArticleColumnSub::class;

	public function info($columnId, $uid)
	{
		return ArticleColumnSub::query()->where('column_id', $columnId)->where('user_id', $uid)->first();
	}

	public function sub($columnId, $uid)
	{
		$row = ArticleColumnSub::query()->where('column_id', $columnId)->where('user_id', $uid)->first();
		if ($row) {
			if (in_array($row->status, [ArticleColumnSub::STATUS_CREATER, ArticleColumnSub::STATUS_SUB])) {
				throw new ErrorHttpException('已关注');
			}
			$row->status = ArticleColumnSub::STATUS_SUB;
			$row->save();
		} else {
			$column = ArticleColumn::query()->find($columnId);
			if (!$column) {
				throw new ErrorHttpException('专栏不存在');
			}
			$subData = [
				'column_id' => $column->id,
				'user_id' => $uid,
				'creater_id' => $column->user_id,
				'status' => ArticleColumnSub::STATUS_SUB,
				'sub_time' => time()
			];
			$row = ArticleColumnSub::query()->create($subData);
		}
		return $row;
	}

	public function unSub($columnId, $uid)
	{
		$row = ArticleColumnSub::query()->where('column_id', $columnId)->where('user_id', $uid)->first();
		if ($row) {
			if ($row->status == ArticleColumnSub::STATUS_CREATER) {
				throw new ErrorHttpException('不能取关自己的栏目');
			}
			if ($row->status == ArticleColumnSub::STATUS_SUB) {
				$row->status = ArticleColumnSub::STATUS_NO;
				$row->save();
				return $row;
			}
		}
		throw new ErrorHttpException('用户尚未关注');
	}
}
