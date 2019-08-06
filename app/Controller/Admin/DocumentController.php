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

use W7\App\Event\ChangeAuthEvent;
use W7\App\Model\Entity\PermissionDocument;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends Controller
{
	public function __construct()
	{
		$this->logic = new DocumentLogic();
		$this->user = new UserLogic();
	}

	public function getlist(Request $request)
	{
		try {
			$res = $this->logic->getlist($request->document_user_auth, $request->document_user_id,$request->input('page'));
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function getDocUserList(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required',
			], [
				'id.required' => '文档不能为空',
			]);

			$res = $this->logic->getDocUserList($request->input('id'), $request->document_user_id, $request->document_user_auth);
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function getdetails(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => '文档ID不能为空',
			]);
			$res = $this->logic->getdetails($request->input('id'), $request->document_user_id, $request->document_user_auth);
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function create(Request $request)
	{
		try {
			$this->validate($request, [
				'name' => 'required',
			], [
				'name.required' => '文档名称不能为空',
			]);

			$name = trim($request->input('name'));

			if (!$request->document_user_id) {
				return $this->error('用户不存在');
			}
			$data = [];
			$data['name'] = $name;
			$data['creator_id'] = $request->document_user_id;
			if ($request->input('description')) {
				$data['description'] = $request->input('description');
			} else {
				$data['description'] = '';
			}
			$data['is_show'] = 2;

			$res = $this->logic->create($data);
			if ($res) {
				PermissionDocument::create(['user_id' => $data['creator_id'],'document_id' => $res['id']]);
				ChangeAuthEvent::instance()->attach('user_id', $data['creator_id'])->attach('document_id', $res['id'])->dispatch();
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function update(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => '文档ID不能为空',
			]);
			$documentId = $request->input('id');

			$relation = $this->logic->relation($request->document_user_id, $documentId);
			if ($relation !== true) {
				return $this->error($relation);
			}

			$data = [];
			if ($request->input('name')) {
				$data['name'] = $request->input('name');
			}
			if ($request->input('description')) {
				$data['description'] = $request->input('description');
			}
			if ($request->input('is_show')) {
				$data['is_show'] = (int)$request->input('is_show');
			}
			$res = $this->logic->update($documentId, $data);
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function del(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => '文档ID不能为空',
			]);
			$relation = $this->logic->relation($request->document_user_id, $request->input('id'));
			if ($relation !== true) {
				return $this->error($relation);
			}

			idb()->beginTransaction();
			$res = $this->logic->del($request->input('id'));
			if ($res) {
				$this->chapter = new ChapterLogic();
				$this->chapter->deleteDocument($request->input('id'));
				ChangeAuthEvent::instance()->attach('user_id', 0)->attach('document_id', $res['id'])->dispatch();
				idb()->commit();
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			idb()->rollBack();
			return $this->error($e->getMessage());
		}
	}

	public function search(Request $request)
	{
		try {
			$this->validate($request, [
				'name' => 'required',
			], [
				'name.required' => '文档名称不能为空',
			]);
			$res = $this->logic->search(trim($request->input('name')), $request->document_user_id, $request->document_user_auth,$request->input('page'));
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
