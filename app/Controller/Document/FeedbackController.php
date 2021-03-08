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

namespace W7\App\Controller\Document;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\DocumentFeedback;
use W7\Http\Message\Server\Request;

class FeedbackController extends BaseController
{

	/**
	 * 提交反馈
	 * @api {post} /document/feedback/add 文档API-提交
	 * @apiName add
	 * @apiGroup document.Feedback
	 *
	 * @apiParam {Number} document_id 文档ID
	 * @apiParam {String} type 问题类型  格式以 , 隔开
	 * @apiParam {String} content 问题内容
	 * @apiParam {Array}  images 图片
	 */
	public function add(Request $request)
	{
		//验证
		$params = $this->validate($request,[
			'document_id' => 'required|integer|min:1',
			'type' => 'string|required',
			'content' => 'string|required|max:300'
		],[
			'document_id.required' => '文档id必填',
			'document_id.min' => '文档id最小为0',
			'type.required' => '问题类型必选',
			'content.required' => '反馈内容必填',
			'content.max' => '反馈内容最大300个字符'
		]);

		$user = $request->getAttribute('user');
		//图片
		$images = $request->post('images');

		$result = DocumentFeedback::query()->create([
			'user_id' => $user->id ? : 0,
			'document_id' => $params['document_id'],
			'type' => trim($params['type']),
			'content' => htmlspecialchars(trim($params['content']),ENT_QUOTES),
			'images'  => $images ? json_encode($images) : ''
		]);

		if (!$result) {
			throw new ErrorHttpException('提交反馈失败');
		}

		return $this->data('success');

	}
}
