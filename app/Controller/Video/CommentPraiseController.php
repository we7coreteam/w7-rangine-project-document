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

namespace W7\App\Controller\Video;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Video\CommentPraiseLogic;
use W7\Http\Message\Server\Request;

class CommentPraiseController extends BaseController
{
	protected function block()
	{
		return new CommentPraiseLogic();
	}

	/**
	 * @api {post} /video/commentPraise/praise 视频评论点赞-点赞
	 * @apiName praise
	 * @apiGroup videoCommentPraise
	 *
	 * @apiParam {Number} comment_id 视频评论ID
	 **/
	public function praise(Request $request)
	{
		$data = $this->validate($request, [
			'comment_id' => 'required|integer',
		], [
			'comment_id' => '评论ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->praise($data['comment_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /video/commentPraise/unPraise 视频评论点赞-取消点赞
	 * @apiName unPraise
	 * @apiGroup videoCommentPraise
	 *
	 * @apiParam {Number} comment_id 视频评论ID
	 **/
	public function unPraise(Request $request)
	{
		$data = $this->validate($request, [
			'comment_id' => 'required|integer',
		], [
			'comment_id' => '评论ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->unPraise($data['comment_id'], $user->id);
		return $this->data($result);
	}
}
