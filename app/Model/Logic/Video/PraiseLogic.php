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

use W7\App\Model\Entity\Video;
use W7\App\Model\Entity\Video\Praise;
use W7\App\Model\Logic\BaseLogic;
use W7\App\Exception\ErrorHttpException;

class PraiseLogic extends BaseLogic
{
	protected $model = Praise::class;

	public function praise($videoId, $uid)
	{
		$video = Video::query()->find($videoId);
		if (!$video) {
			throw new ErrorHttpException('视频不存在');
		}
		$num = 1;
		$row = Praise::query()
			->where('video_id', $videoId)
			->where('user_id', $uid)
			->first();
		try {
			idb()->beginTransaction();
			if ($row) {
				throw new ErrorHttpException('您已点赞');
			}
			$saveData = [
				'video_id' => $videoId,
				'user_id' => $uid,
			];
			Praise::query()->create($saveData);
			$video->increment('praise_num', $num);
			idb()->commit();
			return $video;
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function unPraise($videoId, $uid)
	{
		$video = Video::query()->find($videoId);
		if (!$video) {
			throw new ErrorHttpException('视频不存在');
		}
		$num = 1;
		$row = Praise::query()
			->where('video_id', $videoId)
			->where('user_id', $uid)
			->first();
		try {
			idb()->beginTransaction();
			if (!$row) {
				throw new ErrorHttpException('您未点赞');
			}
			$row->delete();
			//点赞数量-1
			$video->decrement('praise_num', $num);
			idb()->commit();
			return $video;
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function info($videoId, $uid)
	{
		return Praise::query()
			->where('video_id', $videoId)
			->where('user_id', $uid)
			->first();
	}
}
