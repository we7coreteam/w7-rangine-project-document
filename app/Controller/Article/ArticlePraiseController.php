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
use W7\App\Model\Logic\Article\ArticlePraiseLogic;
use W7\Http\Message\Server\Request;

class ArticlePraiseController extends BaseController
{
	protected function block()
	{
		return new ArticlePraiseLogic();
	}

	/**
	 * @api {post} /article/articlePraise/info 点赞-获取当前文章是否点赞
	 * @apiName info
	 * @apiGroup articlePraise
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已点赞
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"article_id":"1","user_id":1,"status":1,"praise_time":1619063551,"updated_at":"1619063551","created_at":"1619063551","id":1},"message":"ok"}
	 */
	public function info(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->info($data['article_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articlePraise/praise 点赞-新增点赞
	 * @apiName praise
	 * @apiGroup articlePraise
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已点赞0未点赞
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"article_comment_praise":{"id":1,"article_id":1,"comment_id":1,"user_id":1,"status":1,"praise_time":1619515858,"created_at":"1619515859","updated_at":"1619516166"},"article_comment":{"id":1,"article_id":1,"user_id":1,"comment":"写的不错","status":1,"praise_num":1,"created_at":"1619075127","updated_at":"1619515871"}},"message":"ok"}
	 */
	public function praise(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->praise($data['article_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articlePraise/unPraise 点赞-取消点赞
	 * @apiName unPraise
	 * @apiGroup articlePraise
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已点赞0未点赞
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"article_comment_praise":{"id":1,"article_id":1,"comment_id":1,"user_id":1,"status":0,"praise_time":1619515858,"created_at":"1619515859","updated_at":"1619516186"},"article_comment":{"id":1,"article_id":1,"user_id":1,"comment":"写的不错","status":1,"praise_num":0,"created_at":"1619075127","updated_at":"1619516166"}},"message":"ok"}
	 */
	public function unPraise(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->unPraise($data['article_id'], $user->id);
		return $this->data($result);
	}
}
