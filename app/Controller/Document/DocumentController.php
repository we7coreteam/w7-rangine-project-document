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
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends BaseController
{
	public function detail(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法'
		]);

		if ($user = $request->getAttribute('user')) {
			UserOperateLog::query()->create([
				'user_id' => $user->id,
				'document_id' => $params['document_id'],
				'chapter_id' => 0,
				'operate' => UserOperateLog::PREVIEW
			]);
		}

		$res = DocumentLogic::instance()->getById($params['document_id']);
		return $this->data($res);
	}
}
