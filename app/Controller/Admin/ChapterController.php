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
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends BaseController
{
	public function detail(Request $request)
	{
		$this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传',
		]);
		$documentId = intval($request->input('document_id'));
		$document = DocumentLogic::instance()->getById($documentId);

		$chapter = ChapterLogic::instance()->getCatalog($documentId);
		return $this->data($chapter);
	}

	public function create(Request $request)
	{
		$this->validate($request, [
			'name' => 'string|required|max:30',
			'sort' => 'required|integer|min:0',
			'document_id' => 'required|integer|min:1',
			'parent_id' => 'required|integer|min:0',
		], [
			'name.required' => '章节名称必填',
			'name.max' => '章节名最大３０个字符',
			'sort.min' => '排序最小值为０',
			'sort.required' => '排序必填',
			'document_id.required' => '文档id必填',
			'document_id.min' => '文档id最小为0',
			'parent_id.required' => '父id必填',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$parentId = intval($request->post('parent_id'));
		if (!empty($parentId)) {
			$parentChapter = ChapterLogic::instance()->getById($parentId);
			if (empty($parentChapter)) {
				throw new ErrorHttpException('父章节不存在');
			}
		}

		Chapter::query()->create([
			'name' => $request->post('name'),
			'sort' => intval($request->post('sort')),
			'document_id' => intval($request->post('document_id')),
			'parent_id' => $parentId,
		]);

		return $this->data('success');
	}

	public function update(Request $request)
	{
		try {
			$this->validate($request, [
				'name' => 'string|required|max:30',
				'sort' => 'required|integer|min:0',
				'id' => 'required|integer|min:1',
			], [
				'name.required' => '章节名称必填',
				'name.max' => '章节名最大３０个字符',
				'sort.min' => '排序最小值为０',
				'sort.required' => '排序必填',
				'id.required' => '文档id必填',
				'id.min' => '文档id最小为0',
			]);

			$data['name'] = $request->input('name');
			$data['sort'] = $request->input('sort');
			$id = $request->input('id');

			$result = $this->logic->updateChapter($id, $data);
			if ($result) {
				return $this->success($result);
			}

			return $this->error($result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function destroy(Request $request)
	{
		$this->validate($request, [
			'id' => 'required|integer',
		], [
			'id.required' => 'id is required',
		]);
		$id = $request->input('id');

		try {
			$res = $this->logic->deleteChapter($id);
			if ($res) {
				return $this->success();
			} else {
				return $this->error();
			}
		} catch (\Exception $e) {
			idb()->rollBack();
			return $this->error($e->getMessage());
		}
	}

	public function saveContent(Request $request)
	{
		try {
			$this->validate($request, [
				'chapter_id' => 'required|integer|min:1',
				'layout' => 'required|integer|min:1',
			], [
				'chapter_id.required' => '文档id必填',
				'layout' => '文档布局必填',
			]);
			$id = $request->input('chapter_id');
			$content = $request->input('content');
			$layout = $request->input('layout');
			$res = $this->logic->saveContent($id, $content, $layout);
			if ($res) {
				$res['layout'] = $layout;
				return $this->success($res);
			}
			return $this->error('保存失败');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function getContent(Request $request)
	{
		try {
			$this->validate($request, [
				'chapter_id' => 'required|integer|min:1',
			], [
				'chapter_id.required' => '文档id必填',
				'chapter_id.min' => '文档id最小为0',
			]);
			$id = $request->input('chapter_id');
			$content = $this->logic->getContent($id);
			return $this->success($content);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function searchChapter(Request $request)
	{
		try {
			$this->validate($request, [
				'keywords' => 'required',
				'document_id' => 'required|integer|min:1',
			], [
				'document_id.required' => '文档id必填',
				'document_id.min' => '文档id最小为0',
				'keywords.required' => '关键字必填',
			]);
			$id = $request->input('document_id');
			$keywords = $request->input('keywords');
			$result = $this->logic->searchChapter($id, $keywords);
			return $this->success($result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
