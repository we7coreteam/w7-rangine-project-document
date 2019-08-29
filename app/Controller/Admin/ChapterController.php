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

use W7\App\Model\Logic\ChapterLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends Controller
{
	public function __construct()
	{
		$this->logic = new ChapterLogic();
	}

	public function index(Request $request)
	{
		try {
			$this->validate($request, [
				'document_id' => 'required|integer',
			], [
				'document_id.required' => '文档ID必传',
			]);
			$id = (int)$request->input('document_id');

			$auth = $request->document_user_auth;
			$documentAuth = $this->documentAuth($id, $auth);
			if ($documentAuth['status'] == false) {
				return $this->error($documentAuth['msg']);
			}

			$result = $this->logic->getChapters($id);

			return $this->success($result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function create(Request $request)
	{
		try {
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

			$data['name'] = $request->input('name');
			$data['sort'] = $request->input('sort', 0);
			$data['document_id'] = $request->input('document_id');
			$data['parent_id'] = $request->input('parent_id');

			$auth = $request->document_user_auth;
			$documentAuth = $this->documentAuth($data['document_id'], $auth);
			if ($documentAuth['status'] == false) {
				return $this->error($documentAuth['msg']);
			}

			$result = $this->logic->createChapter($data);
			if ($result) {
				return $this->success($result);
			}

			return $this->error($result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
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
			$data['auth'] = $request->document_user_auth;
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
			idb()->beginTransaction();
			$res = $this->logic->deleteChapter($id, $request->document_user_auth);
			if ($res) {
				idb()->commit();
				return $this->success();
			} else {
				idb()->rollBack();
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
			$content = $request->input('content', '');
			$layout = $request->input('layout', '');
			$res = $this->logic->saveContent($id, $content, $layout, $request->document_user_auth);
			if ($res){
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
			$content = $this->logic->getContent($id, $request->document_user_auth);
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
