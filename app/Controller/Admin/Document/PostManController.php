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

namespace W7\App\Controller\Admin\Document;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Document\PostMan\PostManLogic;
use W7\Http\Message\Server\Request;

class PostManController extends BaseController
{
	/**
	 * @api {post} /document/postman/postManJsonToDocument POSTMAN Collection V2格式JSON导入到项目根目录
	 * @apiName postManJsonToDocument
	 * @apiGroup PostMan
	 *
	 * @apiParam {String} json POSTMAN Collection V2格式JSON
	 */
	public function postManJsonToDocument(Request $request)
	{
		$this->validate($request, [
			'json' => 'required',
		]);
		$user = $request->getAttribute('user');
		$postManLogic = new PostManLogic();
		$data = $postManLogic->postManJsonToDocument($user->id, $request->post('json'));
		return $data;
	}

	/**
	 * @api {post} /document/postman/documentToPostManJosn 将项目根目录转成POSTMAN Collection V2格式JSON
	 * @apiName documentToPostManJosn
	 * @apiGroup PostMan
	 *
	 * @apiParam {Number} document_id 章节ID
	 */
	public function documentToPostManJosn(Request $request)
	{
		$this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档id必填',
		]);
		$postManLogic = new PostManLogic();
		$data = $postManLogic->documentToPostManJosn(intval($request->post('document_id')));
		return $data;
	}
}
