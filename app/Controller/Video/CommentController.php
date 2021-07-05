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
use W7\App\Model\Logic\Video\CommentLogic;
use W7\Http\Message\Server\Request;

class CommentController extends BaseController
{
	protected function block()
	{
		return new CommentLogic();
	}

	protected $query = [
		'=' => ['video_id']
	];

	public function handleValidate(Request $request)
	{
		return $this->validate($request, [
			'video_id' => 'required|integer|gt:0',
			'comment' => 'required|string',
		], [
			'video_id' => '视频ID',
			'comment' => '评论内容',
		]);
	}

	/**
	 * @api {get} /video/comment 视频评论-列表
	 * @apiName index
	 * @apiGroup videoComment
	 *
	 * @apiParam {Number} video_id 视频ID
	 *
	 * @apiSuccess {String} comment 评论内容
	 * @apiSuccess {Number} is_praise 是否点赞
	 * @apiSuccess {Object} user 用户信息
	 * @apiSuccess {String} user.username 用户昵称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":2,"video_id":2,"comment":"testes111","user_id":1,"created_at":"1624604823","updated_at":"1624604823","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":42}},{"id":1,"video_id":2,"comment":"testes","user_id":1,"created_at":"1624602540","updated_at":"1624602540","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":42}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":2,"total":2},"message":"ok"}
	 **/
	public function index(Request $request)
	{
		$this->validate($request, [
			'video_id' => 'required|integer|gt:0',
		], [
			'video_id' => '视频ID',
		]);
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$condition = $this->block()->handleCondition($this->query);
		$user = $request->session->get('user');
		$comments = $this->block()->index($condition, $page, $pageSize, 'user', 'created_at desc');
		if ($user) {
			$comments = $this->block()->isPraise($comments, $user);
		}
		return $this->data($comments);
	}

	/**
	 * @api {post} /video/comment 视频评论-新增
	 * @apiName store
	 * @apiGroup videoComment
	 *
	 * @apiParam {Number} video_id 视频ID
	 * @apiParam {String} comment 评论内容
	 **/
	public function store(Request $request)
	{
		$data = $this->handleValidate($request);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->store($data);
		return $this->data($result);
	}
}
