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

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\UserShareLogic;
use W7\Http\Message\Server\Request;

class UserShareController extends BaseController
{
	public function shareUrl(Request $request)
	{
		$params = $this->validate($request, [
			'chapter_id' => 'required|integer',
			'document_id' => 'required|integer'
		]);

		$user = $request->getAttribute('user');

		return $this->data(UserShareLogic::instance()->getShareUrl($user->id, $params['document_id'], $params['chapter_id']));
	}

	/**
	 * @api {get} /admin/share/articleUrl 分享-获取文章分享链接
	 * @apiName articleUrl
	 * @apiGroup share
	 *
	 * @apiParam {Number} article_id 文章id
	 *
	 * @apiSuccess {String} data 分享链接
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":"http:\/\/wiki.we7888.cn\/articleDetail?id=1&share_key=bcfd2Fyx6Io7s9bk%2BlrLzlBZ6EeIAHMJBqxvKc12xvrFQA","message":"ok"}
	 */
	public function articleShareUrl(Request $request)
	{
		$params = $this->validate($request, [
			'article_id' => 'required|integer'
		]);

		$user = $request->getAttribute('user');

		return $this->data(UserShareLogic::instance()->getArticleShareUrl($user->id, $params['article_id']));
	}
}
