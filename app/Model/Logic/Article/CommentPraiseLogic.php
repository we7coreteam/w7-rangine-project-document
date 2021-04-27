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
use W7\App\Model\Entity\Article\ArticleComment;
use W7\App\Model\Entity\Article\CommentPraise;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class CommentPraiseLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = CommentPraise::class;

	public function info($commentId, $uid)
	{
		return CommentPraise::query()
			->where('comment_id', $commentId)
			->where('user_id', $uid)
			->where('status', CommentPraise::STATUS_YES)
			->first();
	}

	public function praise($commentId, $uid)
	{
		$articleComment = ArticleComment::query()->find($commentId);
		if (!$articleComment) {
			throw new ErrorHttpException('文章评论不存在');
		}
		$num = 1;
		$row = CommentPraise::query()
			->where('comment_id', $commentId)
			->where('user_id', $uid)
			->first();
		try {
			idb()->beginTransaction();
			if ($row) {
				if ($row->status == CommentPraise::STATUS_YES) {
					throw new ErrorHttpException('您已点赞');
				}
				$row->status = CommentPraise::STATUS_YES;
				$row->save();
			} else {
				$saveData = [
					'article_id' => $articleComment->article_id,
					'comment_id' => $commentId,
					'user_id' => $uid,
					'status' => CommentPraise::STATUS_YES,
					'praise_time' => time()
				];
				$row = CommentPraise::query()->create($saveData);
			}
			//点赞数量+1
			$articleComment->increment('praise_num', $num);
			idb()->commit();
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		return $row;
	}

	public function unPraise($commentId, $uid)
	{
		$articleComment = ArticleComment::query()->find($commentId);
		if (!$articleComment) {
			throw new ErrorHttpException('文章评论不存在');
		}
		$num = 1;
		$row = CommentPraise::query()
			->where('comment_id', $commentId)
			->where('user_id', $uid)
			->first();
		try {
			idb()->beginTransaction();
			if ($row) {
				if ($row->status == CommentPraise::STATUS_NO) {
					throw new ErrorHttpException('您已取消点赞');
				}
				$row->status = CommentPraise::STATUS_NO;
				$row->save();
				//点赞数量-1
				if ($articleComment->praise_num > $num) {
					$articleComment->decrement('praise_num', $num);
				}
			}
			idb()->commit();
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		return $row;
	}
}
