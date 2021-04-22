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
use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Entity\Article\ArticleColumnSub;
use W7\App\Model\Logic\Article\ArticleLogic;
use W7\Http\Message\Server\Request;

class ArticleController extends BaseController
{
	protected function block()
	{
		return new ArticleLogic();
	}

	protected $query = [
		'=' => ['status', 'column_id'],
		'like' => ['title']
	];

	/**
	 * @api {get} /article 全部文章-列表
	 * @apiName index
	 * @apiGroup article
	 *
	 * @apiParam {Number} column_id 栏目名称
	 * @apiParam {String} title 文章标题
	 * @apiParam {Number} tag_ids 标签ID
	 * @apiParam {Number} is_sub 是否筛选1已订阅(包含自己)2订阅不含自己
	 *
	 * @apiSuccess {Number} column_id 栏目ID
	 * @apiSuccess {Array} tag_ids 标签列表
	 * @apiSuccess {Number} user_id 用户ID
	 * @apiSuccess {String} title 文章标题
	 * @apiSuccess {String} content 文章内容
	 * @apiSuccess {Number} comment_status 是否开启评论
	 * @apiSuccess {Number} is_reprint 文章来源
	 * @apiSuccess {Number} reprint_url 来源链接
	 * @apiSuccess {Number} home_thumbnail 首页缩略图
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 * @apiSuccess {Number} status 状态0待审核1已审核2审核失败
	 * @apiSuccess {Number} reason 驳回描述
	 * @apiSuccess {Array} tags 标签信息
	 * @apiSuccess {Object} tags.tag_config 标签信息
	 * @apiSuccess {String} tags.tag_config.name 标签名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":4,"column_id":1,"tag_ids":["1","2"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618912571","updated_at":"1618912571","status_text":"待审核"}],"first_page_url":"\/?=1","from":1,"last_page":4,"last_page_url":"\/?=4","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":4},"message":"ok"}
	 **/

	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->post('page_size', 10));
		$query = Article::query();
		if ($request->input('tag_id', '')) {
			$tagId = $request->input('tag_id');
			if (is_numeric($tagId)) {
				$query->leftJoin('article_tag', 'article_tag.article_id', 'article.id');
				$query->where('article_tag.tag_id', $tagId);
			}
		}
		if ($request->input('is_sub', '')) {
			$isSub = $request->input('is_sub');
			if ($isSub == 1) {
				$query->leftJoin('article_column_sub', 'article_column_sub.column_id', 'article.column_id');
				$query->whereIn('article_column_sub.status', [ArticleColumnSub::STATUS_CREATER, ArticleColumnSub::STATUS_SUB]);
			} elseif ($isSub == 2) {
				$query->leftJoin('article_column_sub', 'article_column_sub.column_id', 'article.column_id');
				$query->where('article_column_sub.status', ArticleColumnSub::STATUS_SUB);
			}
		}
		if ($request->input('column_id', '')) {
			$columnId = $request->input('column_id');
			if (is_numeric($columnId)) {
				$query->where('article.column_id', $columnId);
			}
		}
		if ($request->input('title', '')) {
			$query->where('article.title', 'like', '%' . $request->input('title', '') . '%');
		}
		$query->with('tags')->where('article.status', Article::STATUS_SUCCESS);
		$result = $query->paginate($pageSize, $columns = ['article.*'], '', $page);
		return $this->data($result);
	}

	/**
	 * @api {get} /article/indexMy 个人文章-列表
	 * @apiName indexMy
	 * @apiGroup article
	 *
	 * @apiParam {Number} column_id 栏目名称
	 * @apiParam {Number} status 状态0待审核1已审核2审核失败
	 * @apiParam {String} title 文章标题
	 *
	 * @apiSuccess {Number} column_id 栏目ID
	 * @apiSuccess {Array} tag_ids 标签列表
	 * @apiSuccess {Number} user_id 用户ID
	 * @apiSuccess {String} title 文章标题
	 * @apiSuccess {String} content 文章内容
	 * @apiSuccess {Number} comment_status 是否开启评论
	 * @apiSuccess {Number} is_reprint 文章来源
	 * @apiSuccess {Number} reprint_url 来源链接
	 * @apiSuccess {Number} home_thumbnail 首页缩略图
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 * @apiSuccess {Number} status 状态0待审核1已审核2审核失败
	 * @apiSuccess {Number} reason 驳回描述
	 * @apiSuccess {Array} tags 标签信息
	 * @apiSuccess {Object} tags.tag_config 标签信息
	 * @apiSuccess {String} tags.tag_config.name 标签名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":4,"column_id":1,"tag_ids":["1","2"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618912571","updated_at":"1618912571","status_text":"待审核"}],"first_page_url":"\/?=1","from":1,"last_page":4,"last_page_url":"\/?=4","next_page_url":"\/?=2","path":"\/","per_page":"1","prev_page_url":null,"to":1,"total":4},"message":"ok"}
	 **/
	public function indexMy(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->post('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		$user = $request->getAttribute('user');
		$condition[] = ['user_id', '=', $user->id];
		$result = $this->block()->lists($condition, $page, $pageSize, 'tags');
		return $this->data($result);
	}

	/**
	 * @api {get} /article/:id 全部文章-详情
	 * @apiName show
	 * @apiGroup article
	 *
	 * @apiParam {Number} is_read 增加阅读次数
	 *
	 * @apiSuccess {Number} column_id 栏目ID
	 * @apiSuccess {Array} tag_ids 标签列表
	 * @apiSuccess {Number} user_id 用户ID
	 * @apiSuccess {String} title 文章标题
	 * @apiSuccess {String} content 文章内容
	 * @apiSuccess {Number} comment_status 是否开启评论
	 * @apiSuccess {Number} is_reprint 文章来源
	 * @apiSuccess {Number} reprint_url 来源链接
	 * @apiSuccess {Number} home_thumbnail 首页缩略图
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 * @apiSuccess {Number} status 状态0待审核1已审核2审核失败
	 * @apiSuccess {Number} reason 驳回描述
	 * @apiSuccess {Array} tags 标签信息
	 * @apiSuccess {Object} tags.tag_config 标签信息
	 * @apiSuccess {String} tags.tag_config.name 标签名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"column_id":1,"tag_ids":["1"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"2222","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618911866","updated_at":"1618911866","status_text":"待审核"},"message":"ok"}
	 **/
	public function show(Request $request, $id)
	{
		$isRead = $request->input('is_read', 0);
		if ($isRead) {
			$row = $this->block()->read($id, 'tags');
		} else {
			$row = $this->block()->show($id, 'tags');
		}
		return $this->data($row);
	}

	/**
	 * @api {post} /article 个人文章-新增
	 * @apiName store
	 * @apiGroup article
	 *
	 * @apiParam {Number} column_id 栏目ID
	 * @apiParam {Array} tag_ids 标签列表
	 * @apiParam {String} title 文章标题
	 * @apiParam {String} content 文章内容
	 * @apiParam {Number} comment_status 是否开启评论
	 * @apiParam {Number} is_reprint 文章来源
	 * @apiParam {Number} reprint_url 来源链接
	 * @apiParam {Number} home_thumbnail 首页缩略图
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"column_id":1,"tag_ids":["1"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"2222","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618911866","updated_at":"1618911866","status_text":"待审核"},"message":"ok"}
	 */
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
			'column_id' => 'required|integer|gt:0',
			'tag_ids' => 'required|array|max:5',
			'title' => 'required|string',
			'content' => 'required|string',
			'comment_status' => 'required|in:0,1',
			'is_reprint' => 'required|in:0,1',
			'reprint_url' => 'string|url',
			'home_thumbnail' => 'required|in:0,1',
		], [
			'column_id' => '栏目',
			'tag_ids' => '标签',
			'title' => '标题',
			'content' => '内容',
			'comment_status' => '评论状态',
			'is_reprint' => '文章来源',
			'reprint_url' => '来源地址',
			'home_thumbnail' => '首页缩略图',
		]);
	}

	/**
	 * @api {put} /article 个人文章-修改
	 * @apiName store
	 * @apiGroup article
	 *
	 * @apiParam {Number} column_id 栏目ID
	 * @apiParam {Array} tag_ids 标签列表
	 * @apiParam {String} title 文章标题
	 * @apiParam {String} content 文章内容
	 * @apiParam {Number} comment_status 是否开启评论
	 * @apiParam {Number} is_reprint 文章来源
	 * @apiParam {Number} reprint_url 来源链接
	 * @apiParam {Number} home_thumbnail 首页缩略图
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":1,"column_id":1,"tag_ids":["1"],"user_id":1,"title":"222","content":"111","comment_status":1,"is_reprint":1,"reprint_url":"2222","home_thumbnail":1,"read_num":0,"praise_num":0,"status":0,"reason":"","created_at":"1618911866","updated_at":"1618911866","status_text":"待审核"},"message":"ok"}
	 */
	public function update(Request $request, $id)
	{
		$data = $this->handleValidate($request);
		$user = $request->getAttribute('user');
		//必须本用户修改
		$checkData['user_id'] = $user->id;
		$result = $this->block()->update($id, $data, $checkData);
		return $this->data($result);
	}

	/**
	 * @api {delete} /article/:id 个人文章-删除
	 * @apiName destroy
	 * @apiGroup article
	 *
	 */
	public function destroy(Request $request, $id)
	{
		$user = $request->getAttribute('user');
		//必须本用户修改
		$checkData['user_id'] = $user->id;
		$result = $this->block()->destroy($id, $checkData);
		return $this->data($result);
	}
}
