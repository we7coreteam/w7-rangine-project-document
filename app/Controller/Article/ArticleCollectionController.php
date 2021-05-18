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
use W7\App\Model\Logic\Article\ArticleCollectionLogic;
use W7\Http\Message\Server\Request;

class ArticleCollectionController extends BaseController
{
	protected function block()
	{
		return new ArticleCollectionLogic();
	}

	/**
	 * @api {get} /article/articleCollection/info 收藏文章-获取当前文章是否收藏
	 * @apiName info
	 * @apiGroup articleCollection
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已收藏0未收藏
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":11,"article_id":14,"user_id":1,"status":1,"created_at":"1621326274","updated_at":"1621331752"},"message":"ok"}
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
	 * @api {post} /article/articleCollection/collection 收藏文章-收藏
	 * @apiName collection
	 * @apiGroup articleCollection
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已收藏
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":11,"article_id":14,"user_id":1,"status":1,"created_at":"1621326274","updated_at":"1621335136"},"message":"ok"}
	 */
	public function collection(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->collection($data['article_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleCollection/unCollection 收藏文章-取消收藏
	 * @apiName unCollection
	 * @apiGroup articleCollection
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 0未收藏
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":11,"article_id":14,"user_id":1,"status":0,"created_at":"1621326274","updated_at":"1621335168"},"message":"ok"}
	 */
	public function unCollection(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->unCollection($data['article_id'], $user->id);
		return $this->data($result);
	}
}
