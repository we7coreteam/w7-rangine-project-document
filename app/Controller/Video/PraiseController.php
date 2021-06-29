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
use W7\App\Model\Logic\Video\PraiseLogic;
use W7\Http\Message\Server\Request;

class PraiseController extends BaseController
{
	protected function block()
	{
		return new PraiseLogic();
	}

	/**
	 * @api {post} /video/praise 视频点赞-点赞
	 * @apiName praise
	 * @apiGroup videoPraise
	 *
	 * @apiParam {Number} video_id 视频ID
	 **/
	public function praise(Request $request)
	{
		$data = $this->validate($request, [
			'video_id' => 'required|integer',
		], [
			'video_id' => '视频ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->praise($data['video_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /video/unPraise 视频点赞-取消点赞
	 * @apiName unPraise
	 * @apiGroup videoPraise
	 *
	 * @apiParam {Number} video_id 视频ID
	 **/
	public function unPraise(Request $request)
	{
		$data = $this->validate($request, [
			'video_id' => 'required|integer',
		], [
			'video_id' => '视频ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->unPraise($data['video_id'], $user->id);
		return $this->data($result);
	}
}
