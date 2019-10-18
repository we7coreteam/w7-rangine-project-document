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
use W7\App\Model\Logic\ChapterLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends BaseController
{
	/**
	 * 某一个文档的目录
	 * @param Request $request
	 * @return array
	 */
	public function catalog(Request $request)
	{
		$id = intval($request->input('document_id'));

		if (!$id) {
			throw new ErrorHttpException('文档不存在或是已经被删除');
		}

		try {
			$result = ChapterLogic::instance()->getCatalog($id);
			return $this->data($result);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function detail(Request $request)
	{
		$id = intval($request->input('id'));
		$documentId = intval($request->input('document_id'));

		if (empty($id) || empty($documentId)) {
			throw new ErrorHttpException('章节不存在或是已经被删除');
		}

		try {
			$result = ChapterLogic::instance()->getDetail($id, $documentId);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function search(Request $request)
	{
		try {
			$this->validate($request, [
				'keywords' => 'required',
				'document_id' => 'required|integer|min:1',
			], [
				'keywords.required' => '关键字必填',
				'document_id.required' => '文档id必填',
				'document_id.integer' => '文档id非法'
			]);

			$keyword = $request->input('keywords');
			$id = $request->input('document_id');
			$res = $this->logic->searchDocument($id, $keyword);
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
