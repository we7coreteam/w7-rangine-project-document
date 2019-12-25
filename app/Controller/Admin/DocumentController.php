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
		$showAll = $request->post('show_all');

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
						'is_public' => $row->isPublicDoc,
						'acl' => DocumentPermissionLogic::instance()->getFounderACL(),
					];
				}
			}
		} else {
			$permissions = [DocumentPermission::MANAGER_PERMISSION, DocumentPermission::OPERATOR_PERMISSION];
			if ($showAll) {
				$permissions[] = DocumentPermission::READER_PERMISSION;
			}
			$query = DocumentPermission::query()->where('user_id', '=', $user->id)
					->whereIn('permission', $permissions)
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
					$star = Document\Star::query()->where('user_id', '=', $user->id)->where('document_id', '=', $row->document_id)->first();
					$result['data'][] = [
						'id' => $row->document->id,
						'name' => $row->document->name,
						'author' => [
							'name' => $row->document->user->username
						],
						'has_star' => $star ? true : false,
						'description' => $row->document->descriptionShort,
						'is_public' => $row->document->isPublicDoc,
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

	public function getAllByUid(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('无操作用户权限');
		}

		$params = $this->validate($request, [
			'user_id' => 'required|integer',
		], [
			'user_id.required' => '请指定用户',
		]);

		$name = trim($request->post('name'));
		$isPublic = trim($request->post('is_public'));
		if ($isPublic && !in_array($isPublic, [Document::PUBLIC_DOCUMENT, Document::PRIVATE_DOCUMENT])) {
			throw new ErrorHttpException('参数错误');
		}
		$page = intval($request->post('page', 1));

		$query = Document::query();
		if ($isPublic) {
			$query = $query->where('is_public', '=', $isPublic);
		}
		if ($name) {
			$query = $query->where('name', 'like', '%' . $name . '%');
		}

		//获取私有文档和公有文档的用户身份列表
		$roleList = DocumentPermissionLogic::instance()->getRoleList();
		$readerRoleName = $roleList[DocumentPermission::READER_PERMISSION];
		unset($roleList[DocumentPermission::READER_PERMISSION]);
		$publicRoleList = [];
		foreach ($roleList as $id => $name) {
			$publicRoleList[] = [
				'id' => $id,
				'name' => $name
			];
		}
		$privateRoleList = $publicRoleList;
		$privateRoleList[] = [
			'id' => DocumentPermission::READER_PERMISSION,
			'name' => $readerRoleName
		];

		$list = $query->paginate(null, '*', 'page', $page);
		foreach ($list->items() as $row) {
			/**
			 * @var DocumentPermission $documentPermission
			 */
			$documentPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($row->id, $params['user_id']);
			$result['data'][] = [
				'id' => $row->id,
				'name' => $row->name,
				'description' => $row->descriptionShort,
				'is_public' => $row->isPublicDoc ,
				'cur_role' => $documentPermission ? $documentPermission->permission : 0,
				'role_list' => $row->isPublicDoc ? $publicRoleList : $privateRoleList
			];
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function operateLog(Request $request)
	{
		$name = $request->post('name');
		$page = intval($request->post('page'));
		//时间按天为单位
		$time = intval($request->post('time'));
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$query = Document\ChapterOperateLog::query()->where('user_id', '=', $user->id)->where('operate', '!=', Document\ChapterOperateLog::DELETE)->distinct(['document_id'])->orderByDesc('created_at');
		if ($name) {
			$query->whereHas('document', function ($query) use ($name) {
				return $query->where('name', 'LIKE', "%{$name}%");
			});
		}
		if ($time) {
			$query = $query->where('created_at', '<', time() - 86400 * $time);
		}

		$list = $query->paginate(null, ['user_id', 'document_id', 'operate', 'remark', 'created_at'], 'page', $page);

		$document = $list->items();
		if (!empty($document)) {
			foreach ($document as $i => $row) {
				$star = Document\Star::query()->where('user_id', '=', $user->id)->where('document_id', '=', $row->document_id)->first();
				$result['data'][] = [
					'id' => $row->document->id,
					'name' => $row->document->name,
					'has_star' => $star ? true : false,
					'author' => [
						'name' => $row->document->user->username
					],
					'description' => $row->document->descriptionShort,
					'is_public' => $row->document->isPublicDoc,
					'time' => $row->created_at->toDateTimeString()
				];
			}
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function deleteOperateLog(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传',
		]);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		Document\ChapterOperateLog::query()->where('document_id', '=', $params['document_id'])->where('user_id', '=', $user->id)->delete();

		return $this->data('success');
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
			'is_public' => $document->isPublicDoc,
			'acl' => [
				'has_manage' => $user->isManager,
				'has_edit' => $user->isOperator,
				'has_delete' => $user->isManager,
				'has_read' => $user->isReader
			]
		];

		$hasManager = false;
		$operator = $document->operator()->with('user')->orderBy('permission')->get();
		if (!empty($operator)) {
			$operator->each(function ($row, $i) use (&$result, &$hasManager) {
				$result['operator'][] = [
					'id' => $row->user->id,
					'username' => $row->user->username,
					'acl' => $row->acl,
				];
				if ($row->acl['has_manage']) {
					$hasManager = true;
				}
			});
		}

		$roleList = DocumentPermissionLogic::instance()->getRoleList();
		if ($document->isPublicDoc) {
			unset($roleList[DocumentPermission::READER_PERMISSION]);
		}
		if ($hasManager) {
			unset($roleList[DocumentPermission::MANAGER_PERMISSION]);
		}
		foreach ($roleList as $id => $name) {
			$result['role_list'][] = [
				'id' => $id,
				'name' => $name
			];
		}

		return $this->data($result);
	}

	public function operator(Request $request)
	{
		$this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '请指定文档',
		]);
		$uid = intval($request->post('user_id'));
		$userName = $request->post('user_name');
		if (!$uid && !$userName) {
			throw new ErrorHttpException('参数错误');
		}

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		if (!$uid && $userName) {
			$findUser = UserLogic::instance()->getByUserName($userName);
			if (!$findUser) {
				throw new ErrorHttpException('用户不存在');
			}
			$uid = $findUser->id;
		}
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
			DocumentPermission::READER_PERMISSION
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

		if (!empty($request->input('login_preview'))) {
			$document->is_public =  Document::LOGIN_PREVIEW_DOCUMENT;
		}

		$document->save();

		if ($document->is_public == Document::PUBLIC_DOCUMENT) {
			DocumentPermission::query()->where('document_id', '=', $document->id)->where('permission', '=', DocumentPermission::READER_PERMISSION)->delete();
		}

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

	private function checkPermissionAndGetDocument(Request $request)
	{
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
