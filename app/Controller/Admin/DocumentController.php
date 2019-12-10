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

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use W7\App\Controller\BaseController;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentPermission;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends BaseController
{
	const PAGE_SIZE = 10;

	public function __construct()
	{
		$this->logic = new DocumentLogic();
	}

	public function all(Request $request)
	{
		$keyword = trim($request->input('keyword'));
		$page = intval($request->post('page'));

		$user = $request->getAttribute('user');

		if ($user->isFounder) {
			$query = Document::query()->with('user')->orderByDesc('id');
			if (!empty($keyword)) {
				$query->where('name', 'LIKE', "%{$keyword}%");
			}
			/**
			 * @var LengthAwarePaginator $result
			 */
			$list = $query->paginate(self::PAGE_SIZE, '*', 'page', $page);

			$document = $list->items();
			if (!empty($document)) {
				foreach ($document as $i => $row) {
					$result['data'][] = [
						'id' => $row->id,
						'name' => $row->name,
						'description' => $row->descriptionShort,
						'is_show' => $row->is_show,
						'permission' => [
							'has_delete' => true,
							'has_edit' => true,
							'has_manage' => true,
						]
					];
				}
			}

		} else {
			$query = DocumentPermission::query()->where('user_id', '=', $user->id)
					->whereIn('permission', [DocumentPermission::MANAGER_PERMISSION, DocumentPermission::OPERATOR_PERMISSION, DocumentPermission::READER_PERMISSION])
					->orderByDesc('id')->with('document');
			if (!empty($keyword)) {
				$query->whereHas('document', function ($query) use ($keyword) {
					return $query->where('name', 'LIKE', "%{$keyword}%");
				});
			}

			$list = $query->paginate(self::PAGE_SIZE, '*', 'page', $page);

			$document = $list->items();
			if (!empty($document)) {
				foreach ($document as $i => $row) {
					$result['data'][] = [
						'id' => $row->document->id,
						'name' => $row->document->name,
						'description' => $row->document->descriptionShort,
						'is_show' => $row->document->is_show,
						'permission' => [
							'has_delete' => $row->permission == DocumentPermission::MANAGER_PERMISSION,
							'has_edit' => $row->permission == DocumentPermission::MANAGER_PERMISSION || $row->permission == DocumentPermission::OPERATOR_PERMISSION,
							'has_manage' => $row->permission == DocumentPermission::MANAGER_PERMISSION,
						],
					];
				}
			}
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function getDocUserList(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required',
			], [
				'id.required' => '文档不能为空',
			]);

			$res = $this->logic->getDocUserList($request->input('id'));
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error('文档不存在');
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function detail(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => '文档ID不能为空',
			]);

			$res = $this->logic->getdetails($request->input('id'));
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

			$relation = $this->logic->relation($documentId);
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
		$relation = $this->logic->relation($request->input('id'));
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
