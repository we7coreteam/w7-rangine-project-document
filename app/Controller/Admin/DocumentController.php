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

use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends Controller
{
	public function __construct()
	{
		$this->logic = new DocumentLogic();
	}

	public function getList(Request $request)
	{
		try {
			$this->validate($request, [
				'name' => '',
			]);
			$name = trim($request->input('name'));
			$res = $this->logic->getlist($request->document_user_auth, $request->document_user_id, $request->input('page'), $name);

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

			$res = $this->logic->getDocUserList($request->input('id'), $request->document_user_id);
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error('文档不存在');
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function getDetails(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => '文档ID不能为空',
			]);
			$res = $this->logic->getdetails($request->input('id'), $request->document_user_id);
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error('文档不存在');
			}
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

			$data = [];
			$data['name'] = $name;
			$data['creator_id'] = $request->document_user_id;
			$data['description'] = $request->input('description');

			$res = $this->logic->create($data);
			if ($res) {
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
			if ($request->input('name') !== null) {
				$data['name'] = $request->input('name');
			}
			if ($request->input('description') !== null) {
				$data['description'] = $request->input('description');
			}
			if ($request->input('is_show') !== null) {
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

	public function delete(Request $request)
	{
		$this->validate($request, [
			'id' => 'required|integer|min:1',
		], [
			'id.required' => '文档ID不能为空',
		]);
		$relation = $this->logic->relation($request->document_user_id, $request->input('id'));
		if ($relation !== true) {
			return $this->error($relation);
		}

		try {
			$res = $this->logic->del($request->input('id'));
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error($res);
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
