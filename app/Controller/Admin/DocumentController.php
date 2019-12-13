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
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentPermission;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends BaseController
{
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
			$list = $query->paginate(null, '*', 'page', $page);

			$document = $list->items();
			if (!empty($document)) {
				foreach ($document as $i => $row) {
					$result['data'][] = [
						'id' => $row->id,
						'name' => $row->name,
						'description' => $row->descriptionShort,
						'is_public' => $row->is_public,
						'acl' => DocumentPermissionLogic::instance()->getFounderACL(),
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

			$list = $query->paginate(null, '*', 'page', $page);

			$document = $list->items();
			if (!empty($document)) {
				foreach ($document as $i => $row) {
					$result['data'][] = [
						'id' => $row->document->id,
						'name' => $row->document->name,
						'description' => $row->document->descriptionShort,
						'is_public' => $row->document->is_public,
						'acl' => $row->acl,
					];
				}
			}
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function detail(Request $request)
	{
		$document = $this->checkPermissionAndGetDocument($request);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$result = [
			'id' => $document->id,
			'name' => $document->name,
			'description' => $document->description,
			'is_public' => $document->is_public,
			'acl' => [
				'has_manage' => $user->isManager,
				'has_edit' => $user->isOperator,
				'has_delete' => $user->isManager,
				'has_read' => $user->isReader
			]
		];

		$roleList = DocumentPermissionLogic::instance()->getRoleList();
		if ($document->is_public == Document::PUBLIC_DOCUMENT) {
			unset($roleList[DocumentPermission::READER_PERMISSION]);
		}
		foreach ($roleList as $id => $name) {
			$result['role_list'][] = [
				'id' => $id,
				'name' => $name
			];
		}

		$operator = $document->operator()->with('user')->orderBy('permission')->get();
		if (!empty($operator)) {
			$operator->each(function ($row, $i) use (&$result) {
				$result['operator'][] = [
					'id' => $row->user->id,
					'username' => $row->user->username,
					'acl' => $row->acl,
				];
			});
		}

		return $this->data($result);
	}

	public function operator(Request $request)
	{
		$this->validate($request, [
			'user_id' => 'required|integer',
			'document_id' => 'required|integer',
		], [
			'user_id.required' => '请指定用户',
			'document_id.required' => '请指定文档',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$uid = intval($request->post('user_id'));
		$permission = intval($request->post('permission'));
		$documentId = intval($request->post('document_id'));

		if ($uid == $user->id) {
			throw new ErrorHttpException('不能添加自己为管理员');
		}

		/**
		 * permission 值不存在时，意味着删除权限
		 * 只要权限合适，减少判断直接删除
		 */
		if (empty($permission)) {
			$hasPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($documentId, $uid);
			if (!empty($hasPermission)) {
				$hasPermission->delete();
			}
			return $this->data('success');
		}

		if (!in_array($permission, [
			DocumentPermission::MANAGER_PERMISSION,
			DocumentPermission::OPERATOR_PERMISSION,
			DocumentPermission::READER_PERMISSION,
		])) {
			throw new ErrorHttpException('您操作了不存在的权限');
		}

		if ($permission == DocumentPermission::MANAGER_PERMISSION && !$user->isFounder) {
			throw new ErrorHttpException('您没有权限添加管理员');
		}

		$document = DocumentLogic::instance()->getById($documentId);
		if (empty($document)) {
			throw new ErrorHttpException('管理的文档的不存在或是已经被删除');
		}

		$operator = UserLogic::instance()->getByUid($uid);
		if (empty($operator)) {
			throw new ErrorHttpException('您操作的用户不存在');
		}

		$hasPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($documentId, $uid);
		if (empty($hasPermission)) {
			$hasPermission = new DocumentPermission();
			$hasPermission->user_id = $uid;
			$hasPermission->document_id = $documentId;
		}
		$hasPermission->permission = $permission;
		$hasPermission->save();

		return $this->data('success');
	}

	public function create(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
		], [
			'name.required' => '文档名称不能为空',
		]);

		$user = $request->getAttribute('user');

		$docuemnt = Document::query()->create([
			'name' => trim($request->input('name')),
			'description' => trim($request->input('description')),
			'creator_id' => $user->id,
			'is_public' => intval($request->post('is_public')) ?? 1,
		]);

		DocumentLogic::instance()->createCreatorPermission($docuemnt);

		return $this->data('success');
	}

	public function update(Request $request)
	{
		$document = $this->checkPermissionAndGetDocument($request);

		if (!empty($request->input('name'))) {
			$document->name = $request->input('name');
		}

		if (!empty($request->input('description'))) {
			$document->description = $request->input('description');
		}

		if (!empty($request->input('is_public'))) {
			$document->is_public = intval($request->input('is_public'));
		}

		$document->save();

		return $this->data('success');

	}

	public function delete(Request $request)
	{
		$document = $this->checkPermissionAndGetDocument($request);
		try {
			DocumentLogic::instance()->deleteByDocument($document);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
		return $this->data('success');
	}

	private function checkPermissionAndGetDocument(Request $request) {
		$this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '请指定文档',
		]);
		$documentId = intval($request->input('document_id'));

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$document = DocumentLogic::instance()->getById($documentId);
		if (empty($document)) {
			throw new ErrorHttpException('您操作的文档不存在');
		}

		return $document;
	}
}
