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
use W7\App\Model\Entity\Video;
use W7\App\Model\Logic\VideoLogic;
use W7\Http\Message\Server\Request;

class VideoController extends BaseController
{
	protected function block()
	{
		return new VideoLogic();
	}

	public function handleValidate(Request $request)
	{
		return $this->validate($request, [
			'title' => 'required|string',
			'cover' => 'required|string',
			'url' => 'required|string',
			'time_length' => 'required|string',
			'category_ids' => 'required|array|max:3',
			'description' => 'string',
			'is_reprint' => 'required|in:0,1',
			'reprint_url' => 'string|url',
		], [], [
			'title' => '标题',
			'cover' => '封面',
			'url' => '视频',
			'time_length' => '时长',
			'category_ids' => '分类',
			'description' => '简介',
			'is_reprint' => '视频来源',
			'reprint_url' => '转载地址',
		]);
	}

	/**
	 * @api {get} /video/home 视频-首页数据
	 * @apiName home
	 * @apiGroup video
	 *
	 * @apiSuccess {Object} carousel 轮播数据
	 * @apiSuccess {Object} activity 活动数据
	 * @apiSuccess {Object} videoRank 视频排行
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"carousel":[{"id":1,"name":"test1","url":"222","image":"333","created_at":"1624952972","updated_at":"1624952972"}],"activity":[{"id":1,"name":"test1111","url":"222","image":"333","created_at":"1624954318","updated_at":"1624954334"}],"videoRank":[{"id":14,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_ids":["1","3"],"user_id":1,"play_num":10000,"praise_num":0,"is_reprint":0,"reprint_url":"https:\/\/www.baidu.com","status":0,"reason":"","created_at":"1624951081","updated_at":"1624951081","time_str":"1小时前","play_num_text":"10.0k","category":[{"id":26,"category_id":1,"video_id":14,"created_at":"1624951081","updated_at":"1624951081","category_config":{"id":1,"name":"test1","created_at":"1624938553","updated_at":"1624938553"}},{"id":27,"category_id":3,"video_id":14,"created_at":"1624951081","updated_at":"1624951081","category_config":{"id":3,"name":"test3","created_at":"1624944838","updated_at":"1624954489"}}],"user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":43}}]},"message":"ok"}
	 */
	public function home(Request $request)
	{
		$list = $this->block()->homeData();
		return $this->data($list);
	}

	/**
	 * @api {get} /video 视频-视频列表
	 * @apiName index
	 * @apiGroup video
	 *
	 * @apiParam {Number} category_id 分类id
	 * @apiParam {Number} user_id 用户id
	 *
	 * @apiSuccess {String} title 标题
	 * @apiSuccess {String} url 视频地址
	 * @apiSuccess {String} time_length 视频时长
	 * @apiSuccess {String} cover 封面图
	 * @apiSuccess {String} description 简介
	 * @apiSuccess {String} time_str 发布时间
	 * @apiSuccess {String} play_num_text 播放量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":9,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_ids":["1","3"],"user_id":1,"play_num":0,"praise_num":0,"is_reprint":0,"reprint_url":"https:\/\/www.baidu.com","status":0,"reason":"","created_at":"1624945388","updated_at":"1624945388","category":[{"id":16,"category_id":1,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":1,"name":"test1","created_at":"1624938553","updated_at":"1624938553"}},{"id":17,"category_id":3,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":3,"name":"test3","created_at":"1624944838","updated_at":"1624944838"}}],"user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":43}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$query = Video::query();
		if ($request->input('category_id', '')) {
			$categoryId = $request->input('category_id');
			if (is_numeric($categoryId)) {
				$query->leftJoin('video_category', 'video_category.video_id', 'video.id');
				$query->where('video_category.category_id', $categoryId);
			}
		}
		if ($request->input('user_id', '')) {
			$userId = $request->input('user_id');
			if (is_numeric($userId)) {
				$query->where('user_id', $userId);
			}
		}
		$query->with(['category', 'category.categoryConfig', 'user'])->where('video.status', Video::STATUS_SUCCESS);
		$query->orderBy('video.id', 'desc');
		$list = $query->paginate($pageSize, $columns = ['video.*'], '', $page);
		return $this->data($list);
	}

	/**
	 * @api {get} /video/indexMy 视频-用户视频列表
	 * @apiName indexMy
	 * @apiGroup video
	 *
	 * @apiSuccess {String} title 标题
	 * @apiSuccess {String} url 视频地址
	 * @apiSuccess {String} time_length 视频时长
	 * @apiSuccess {String} cover 封面图
	 * @apiSuccess {String} description 简介
	 * @apiSuccess {String} time_str 发布时间
	 * @apiSuccess {String} play_num_text 播放量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":9,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_ids":["1","3"],"user_id":1,"play_num":0,"praise_num":0,"is_reprint":0,"reprint_url":"https:\/\/www.baidu.com","status":0,"reason":"","created_at":"1624945388","updated_at":"1624945388","category":[{"id":16,"category_id":1,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":1,"name":"test1","created_at":"1624938553","updated_at":"1624938553"}},{"id":17,"category_id":3,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":3,"name":"test3","created_at":"1624944838","updated_at":"1624944838"}}],"user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":43}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function indexMy(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$user = $request->getAttribute('user');
		$condition[] = ['user_id', '=', $user->id];
		$list = $this->block()->index($condition, $page, $pageSize, ['category', 'category.categoryConfig', 'user']);
		return $this->data($list);
	}

	/**
	 * @api {get} /video/indexHot 视频-热门视频
	 * @apiName indexHot
	 * @apiGroup video
	 *
	 * @apiSuccess {String} title 标题
	 * @apiSuccess {String} url 视频地址
	 * @apiSuccess {String} time_length 视频时长
	 * @apiSuccess {String} cover 封面图
	 * @apiSuccess {String} description 简介
	 * @apiSuccess {String} time_str 发布时间
	 * @apiSuccess {String} play_num_text 播放量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":9,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_ids":["1","3"],"user_id":1,"play_num":0,"praise_num":0,"is_reprint":0,"reprint_url":"https:\/\/www.baidu.com","status":0,"reason":"","created_at":"1624945388","updated_at":"1624945388","category":[{"id":16,"category_id":1,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":1,"name":"test1","created_at":"1624938553","updated_at":"1624938553"}},{"id":17,"category_id":3,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":3,"name":"test3","created_at":"1624944838","updated_at":"1624944838"}}],"user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":43}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function indexHot(Request $request)
	{
		$list = $this->block()->indexHot();
		return $this->data($list);
	}

	/**
	 * @api {post} /video 视频-发布视频
	 * @apiName store
	 * @apiGroup video
	 *
	 * @apiParam {String} title 标题
	 * @apiParam {String} cover 封面图片地址
	 * @apiParam {String} url 视频地址
	 * @apiParam {String} time_length 视频时长
	 * @apiParam {Array} category_ids 分类id
	 * @apiParam {String} description 简介
	 * @apiParam {Number} is_reprint 是否转载0否1是
	 * @apiParam {String} reprint_url 转载地址
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","category_ids":["1","3"],"description":"简介简介简介","is_reprint":"0","reprint_url":"https:\/\/www.baidu.com","user_id":1,"updated_at":"1624945388","created_at":"1624945388","id":9},"message":"ok"}
	 */
	public function store(Request $request)
	{
		$data = $this->handleValidate($request);
		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->store($data);
		return $this->data($result);
	}

	/**
	 * @api {put} /video/:id 视频-编辑视频
	 * @apiName update
	 * @apiGroup video
	 *
	 * @apiParam {String} title 标题
	 * @apiParam {String} cover 封面图片地址
	 * @apiParam {String} url 视频地址
	 * @apiParam {Array} category_ids 分类id
	 * @apiParam {String} description 简介
	 * @apiParam {Number} is_reprint 是否转载0否1是
	 * @apiParam {String} reprint_url 转载地址
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","category_ids":["1","3"],"description":"简介简介简介","is_reprint":"0","reprint_url":"https:\/\/www.baidu.com","user_id":1,"updated_at":"1624945388","created_at":"1624945388","id":9},"message":"ok"}
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
	 * @api {delete} /video/:id 视频-删除视频
	 * @apiName destroy
	 * @apiGroup video
	 */
	public function destroy(Request $request, $id)
	{
		$user = $request->getAttribute('user');
		//必须本用户修改
		$checkData['user_id'] = $user->id;
		$result = $this->block()->destroy($id, $checkData);
		return $this->data($result);
	}

	/**
	 * @api {get} /video/:id 视频-视频详情
	 * @apiName show
	 * @apiGroup video
	 *
	 * @apiSuccess {String} title 标题
	 * @apiSuccess {String} cover 封面地址
	 * @apiSuccess {String} url 视频地址
	 * @apiSuccess {String} description 简介
	 * @apiSuccess {Number} praise_num 点赞数
	 * @apiSuccess {Number} play_num 播放量
	 * @apiSuccess {Number} is_reprint 是否转载0否1是
	 * @apiSuccess {Object} user 作者信息
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","category_id":"1","description":"简介简介简介","is_reprint":"0","user_id":1,"updated_at":"1624510479","created_at":"1624510479","id":2},"message":"ok"}
	 */
	public function show(Request $request, $id)
	{
		$row = $this->block()->show($id, ['user']);
		if ($row) {
			if ($row->status != Video::STATUS_SUCCESS) {
				$userData = $request->session->get('user');
				//审核未通过-只能看见自己的
				if (!$userData || $userData['uid'] != $row->user_id) {
					$row = [];
				}
			}
		}
		return $this->data($row);
	}

	/**
	 * @api {get} /video/recommend 视频-相关推荐
	 * @apiName recommend
	 * @apiGroup video
	 *
	 * @apiParam {Number} video_id 视频id
	 *
	 * @apiSuccess {String} title 标题
	 * @apiSuccess {String} url 视频地址
	 * @apiSuccess {String} time_length 视频时长
	 * @apiSuccess {String} cover 封面图
	 * @apiSuccess {String} description 简介
	 * @apiSuccess {String} time_str 发布时间
	 * @apiSuccess {String} play_num_text 播放量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":9,"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","description":"简介简介简介","time_length":"","category_ids":["1","3"],"user_id":1,"play_num":0,"praise_num":0,"is_reprint":0,"reprint_url":"https:\/\/www.baidu.com","status":0,"reason":"","created_at":"1624945388","updated_at":"1624945388","category":[{"id":16,"category_id":1,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":1,"name":"test1","created_at":"1624938553","updated_at":"1624938553"}},{"id":17,"category_id":3,"video_id":9,"created_at":"1624945388","updated_at":"1624945388","category_config":{"id":3,"name":"test3","created_at":"1624944838","updated_at":"1624944838"}}],"user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1624342983","follower_num":11,"following_num":11,"article_num":43}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function recommend(Request $request)
	{
		$data = $this->validate($request, [
			'video_id' => 'required|integer|gt:0',
		], [
			'video_id' => '视频ID',
		]);

		$list = $this->block()->recommendVideo($data['video_id']);
		return $this->data($list);
	}

	/**
	 * @api {post} /video/addPlayNum 视频-增加播放量
	 * @apiName addPlayNum
	 * @apiGroup video
	 *
	 * @apiParam {Number} video_id 视频ID
	 **/
	public function addPlayNum(Request $request)
	{
		$data = $this->validate($request, [
			'video_id' => 'required|integer|gt:0',
		], [
			'video_id' => '视频ID',
		]);
		$result = $this->block()->addPlayNum($data['video_id']);
		return $this->data($result);
	}
}
