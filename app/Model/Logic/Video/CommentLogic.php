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

class CommentLogic extends BaseLogic
{
	protected $model = Comment::class;

	public function isPraise($comments, $user)
	{
		$comments->map(function ($item) use ($user) {
			$is_praise = CommentPraise::query()->where([
				['comment_id', '=', $item->id],
				['user_id', '=', $user['uid']],
			])->first();

			if ($is_praise) {
				return $item->is_praise = 1;
			} else {
				return $item->is_praise = 0;
			}
		});
		return $comments;
	}
}
