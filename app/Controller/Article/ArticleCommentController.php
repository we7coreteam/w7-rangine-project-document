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
use W7\App\Model\Entity\Article\ArticleComment;
use W7\App\Model\Logic\Article\ArticleCommentLogic;
use W7\Http\Message\Server\Request;

class ArticleCommentController extends BaseController
{
	protected function block()
	{
		return new ArticleCommentLogic();
	}

	protected $query = [
		'=' => ['article_id']
	];

	/**
	 * @api {get} /article/articleComment 文章评论-列表
	 * @apiName index
	 * @apiGroup articleComment
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Text} comment 评论内容
	 * @apiSuccess {Object} user 用户信息
	 * @apiSuccess {String} user.username 用户昵称
	 * @apiSuccess {Number} is_praise 是否已点赞（该属性只在用户登录情况下存在）1已点赞0未点赞
	 **/
	public function index(Request $request)
	{
		$this->validate($request, [
			'article_id' => 'required|integer|gt:0',
		], [
			'article_id' => '文章ID',
		]);
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		$condition[] = ['status', '=', ArticleComment::STATUS_YES];
		$user = $request->session->get('user');
		$comments = $this->block()->index($condition, $page, $pageSize, 'user', 'praise_num desc');
		if ($user) {
			$comments = $this->block()->isPraise($comments, $user);
		}
		return $this->data($comments);
	}

	public function show(Request $request, $id)
	{
		$result = $this->block()->show($id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleComment 文章评论-新增
	 * @apiName store
	 * @apiGroup articleComment
	 *
	 * @apiParam {Number} article_id 文章ID
	 * @apiParam {Text} comment 评论内容
	 **/
	public function store(Request $request)
	{
		$data = $this->handleValidate($request);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	public function handleValidate(Request $request)
	{
		return $this->validate($request, [
			'article_id' => 'required|integer|gt:0',
			'comment' => 'required|string',
		], [
			'article_id' => '文章ID',
			'comment' => '评论内容',
		]);
	}
}
