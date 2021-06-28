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

use W7\App\Model\Entity\Video;
use W7\App\Exception\ErrorHttpException;

class VideoLogic extends BaseLogic
{
	protected $model = Video::class;

	public function store($data)
	{
		$data['status'] = Video::STATUS_CREATE;
		return parent::store($data);
	}

	public function success($id)
	{
		$row = Video::query()->find($id);
		if ($row) {
			if ($row->status != Video::STATUS_CREATE) {
				throw new ErrorHttpException('当前状态不是待审核状态');
			}
			$row->status = Video::STATUS_SUCCESS;
			$row->save();
			return $row;
		} else {
			throw new ErrorHttpException('审核失败');
		}
	}

	public function reject($id, $reason)
	{
		$row = Video::query()->find($id);
		if ($row) {
			if ($row->status != Video::STATUS_CREATE) {
				throw new ErrorHttpException('当前状态不是待审核状态');
			}
			$row->status = Video::STATUS_FAIL;
			$row->reason = $reason;
			$row->save();
			return $row;
		} else {
			throw new ErrorHttpException('审核失败');
		}
	}
}
