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
			'category_id' => 'required|integer|gt:0',
			'description' => 'string',
			'is_reprint' => 'required|in:0,1',
		], [], [
			'title' => '标题',
			'cover' => '封面',
			'url' => '视频',
			'category_id' => '分类',
			'description' => '简介',
			'is_reprint' => '视频来源',
		]);
	}

	/**
	 * @api {post} /video 视频-发布视频
	 * @apiName store
	 * @apiGroup video
	 *
	 * @apiParam {String} title 标题
	 * @apiParam {String} cover 封面图片地址
	 * @apiParam {String} url 视频地址
	 * @apiParam {Number} category_id 分类id
	 * @apiParam {String} description 简介
	 * @apiParam {Number} is_reprint 是否转载0否1是
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"title":"test","cover":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","url":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/CUqNdJoUvpju1LLH6dVHsnju3a31ALNL.jpeg","category_id":"1","description":"简介简介简介","is_reprint":"0","user_id":1,"updated_at":"1624510479","created_at":"1624510479","id":2},"message":"ok"}
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
				//审核未通过-只能看见自己的
				$userData = $request->session->get('user');
				if ($userData['uid'] != $row->user_id) {
					$row = [];
				}
			}
		}
		return $this->data($row);
	}
}
