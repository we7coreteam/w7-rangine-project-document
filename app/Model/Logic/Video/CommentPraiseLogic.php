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

use W7\App\Model\Entity\Video\Comment;
use W7\App\Model\Entity\Video\CommentPraise;
use W7\App\Model\Logic\BaseLogic;
use W7\App\Exception\ErrorHttpException;

class CommentPraiseLogic extends BaseLogic
{
	protected $model = CommentPraise::class;

	public function praise($commentId, $uid)
	{
		$videoComment = Comment::query()->find($commentId);
		if (!$videoComment) {
			throw new ErrorHttpException('评论不存在');
		}
		$row = CommentPraise::query()
			->where('comment_id', $commentId)
			->where('user_id', $uid)
			->first();
		$num = 1;
		try {
			if ($row) {
				throw new ErrorHttpException('您已点赞');
			} else {
				$now = time();
				$saveData = [
					'video_id' => $videoComment->video_id,
					'comment_id' => $commentId,
					'user_id' => $uid,
					'created_at' => $now,
					'updated_at' => $now
				];
				CommentPraise::query()->create($saveData);
				$videoComment->increment('praise_num', $num);
				return $videoComment;
			}
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function unPraise($commentId, $uid)
	{
		$videoComment = Comment::query()->find($commentId);
		if (!$videoComment) {
			throw new ErrorHttpException('视频不存在');
		}
		$num = 1;
		$row = CommentPraise::query()
			->where('comment_id', $commentId)
			->where('user_id', $uid)
			->first();
		try {
			if (!$row) {
				throw new ErrorHttpException('您未点赞');
			}
			$row->delete();
			//点赞数量-1
			$videoComment->decrement('praise_num', $num);
			return $videoComment;
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}
