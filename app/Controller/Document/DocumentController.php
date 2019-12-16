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
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends BaseController
{
	public function getShowList(Request $request)
	{
		try {
			$data = [];
			if (trim($request->input('name'))) {
				$data['name'] = trim($request->input('name'));
			}
			$res = DocumentLogic::instance()->getShowList($data, $request->input('page'));
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function detail(Request $request)
	{
		try {
			$this->validate($request, [
				'document_id' => 'required|integer|min:1',
			], [
				'document_id.required' => '文档id必填',
				'document_id.integer' => '文档id非法'
			]);
			$res = DocumentLogic::instance()->getById($request->input('document_id'));
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}
