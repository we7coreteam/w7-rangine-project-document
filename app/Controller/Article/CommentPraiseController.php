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

namespace W7\App\Controller\Article;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Article\CommentPraiseLogic;
use W7\Http\Message\Server\Request;

class CommentPraiseController extends BaseController
{
	protected function block()
	{
		return new CommentPraiseLogic();
	}

	/**
	 * @api {post} /article/commentPraise/info 评论点赞-获取当前文章是否点赞
	 * @apiName info
	 * @apiGroup commentPraise
	 *
	 * @apiParam {Number} comment_id 评论ID
	 *
	 * @apiSuccess {Number} status 1已点赞
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"article_id":1,"comment_id":1,"user_id":1,"status":1,"praise_time":1619515289,"created_at":"1619515290","updated_at":"1619515335"},"message":"ok"}
	 */
	public function info(Request $request)
	{
		$data = $this->validate($request, [
			'comment_id' => 'required|integer',
		], [
			'comment_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->info($data['comment_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/commentPraise/praise 评论点赞-新增点赞
	 * @apiName praise
	 * @apiGroup commentPraise
	 *
	 * @apiParam {Number} comment_id 评论ID
	 *
	 * @apiSuccess {Number} status 1已点赞0未点赞
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"article_id":1,"comment_id":1,"user_id":1,"status":1,"praise_time":1619515289,"created_at":"1619515290","updated_at":"1619515335"},"message":"ok"}
	 */
	public function praise(Request $request)
	{
		$data = $this->validate($request, [
			'comment_id' => 'required|integer',
		], [
			'comment_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->praise($data['comment_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/commentPraise/unPraise 评论点赞-取消点赞
	 * @apiName unPraise
	 * @apiGroup commentPraise
	 *
	 * @apiParam {Number} comment_id 评论ID
	 *
	 * @apiSuccess {Number} status 1已点赞0未点赞
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"article_id":1,"comment_id":1,"user_id":1,"status":0,"praise_time":1619515289,"created_at":"1619515290","updated_at":"1619515316"},"message":"ok"}
	 */
	public function unPraise(Request $request)
	{
		$data = $this->validate($request, [
			'comment_id' => 'required|integer',
		], [
			'comment_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->unPraise($data['comment_id'], $user->id);
		return $this->data($result);
	}
}
