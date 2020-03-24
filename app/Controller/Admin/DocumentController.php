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
use W7\App\Model\Entity\Star;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserOperateLog;
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
		$pageSize = intval($request->post('page_size'));
		$isPublic = $request->post('is_public');
		$role = $request->post('role');
		//获取只能阅读的文档列表
		$onlyRead = $request->post('only_reader');

		$user = $request->getAttribute('user');

		if ($user->isFounder && !$onlyRead) {
			$query = Document::query()->with('user')->orderByDesc('id');
			if (!empty($keyword)) {
				$query->where('name', 'LIKE', "%{$keyword}%");
			}
			if (!empty($isPublic)) {
				$query->where('is_public', '=', $isPublic);
			}
			/**
			 * @var LengthAwarePaginator $result
			 */
			$list = $query->paginate($pageSize, '*', 'page', $page);

			$document = $list->items();
			if (!empty($document)) {
				foreach ($document as $i => $row) {
					$star = Star::query()->where('user_id', '=', $user->id)->where('document_id', '=', $row->id)->where('chapter_id', '=', 0)->first();
					$lastOperate = UserOperateLog::query()->where('document_id', '=', $row->id)->whereIn('operate', [UserOperateLog::CREATE, UserOperateLog::DELETE, UserOperateLog::EDIT])->latest()->first();
					$result['data'][] = [
						'id' => $row->id,
						'name' => $row->name,
						'cover' => $row->cover,
						'author' => [
							'name' => $row->user->username
						],
						'operator' => [
							'name' => !empty($lastOperate) ? $lastOperate->operateDesc : '',
							'time' => !empty($lastOperate) ? $lastOperate->created_at->toDateTimeString() : ''
						],
						'star_id' => !empty($star) ? $star->id : '',
						'description' => $row->descriptionShort,
						'is_public' => $row->isPublicDoc,
						'acl' => DocumentPermissionLogic::instance()->getFounderACL(),
					];
				}
			}
		} else {
			$permissions = [DocumentPermission::MANAGER_PERMISSION, DocumentPermission::OPERATOR_PERMISSION];
			//$role代表为创建者
			if ($role == 1) {
				$permissions = [DocumentPermission::MANAGER_PERMISSION];
			}
			//$role为2代表为参与者
			if ($role == 2) {
				$permissions = [DocumentPermission::OPERATOR_PERMISSION];
			}
			if ($onlyRead) {
				$permissions = [DocumentPermission::READER_PERMISSION];
			}
			$query = DocumentPermission::query()->where('user_id', '=', $user->id)
				->whereIn('permission', $permissions)
				->orderByDesc('id')->with('document');
			if (!empty($keyword)) {
				$query->whereHas('document', function ($query) use ($keyword) {
					return $query->where('name', 'LIKE', "%{$keyword}%");
				});
			}
			if (!empty($isPublic)) {
				$query->whereHas('document', function ($query) use ($isPublic) {
					return $query->where('is_public', '=', $isPublic);
				});
			}

			$list = $query->paginate($pageSize, '*', 'page', $page);

			$document = $list->items();
			if (!empty($document)) {
				foreach ($document as $i => $row) {
					$star = Star::query()->where('user_id', '=', $user->id)->where('document_id', '=', $row->document_id)->where('chapter_id', '=', 0)->first();
					$lastOperate = UserOperateLog::query()->where('document_id', '=', $row->id)->whereIn('operate', [UserOperateLog::CREATE, UserOperateLog::DELETE, UserOperateLog::EDIT])->latest()->first();
					$result['data'][] = [
						'id' => $row->document->id,
						'name' => $row->document->name,
						'cover' => $row->document->cover,
						'author' => [
							'name' => $row->document->user->username
						],
						'operator' => [
							'name' => !empty($lastOperate) ? $lastOperate->operateDesc : '',
							'time' => !empty($lastOperate) ? $lastOperate->created_at->toDateTimeString() : ''
						],
						'star_id' => !empty($star) ? $star->id : '',
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
		$publicRoleList[] = [
			'id' => DocumentPermission::OPERATOR_PERMISSION,
			'name' => $roleList[DocumentPermission::OPERATOR_PERMISSION]
		];
		$privateRoleList = $publicRoleList;
		$privateRoleList[] = [
			'id' => DocumentPermission::READER_PERMISSION,
			'name' => $roleList[DocumentPermission::READER_PERMISSION]
		];

		$list = $query->paginate(null, '*', 'page', $page);
		foreach ($list->items() as $row) {
			/**
			 * @var DocumentPermission $documentPermission
			 */
			$documentPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($row->id, $params['user_id']);
			$manager = DocumentPermissionLogic::instance()->getByDocIdAndPermission($row->id, DocumentPermission::MANAGER_PERMISSION);
			$roleList = $row->isPublicDoc ? $publicRoleList : $privateRoleList;
			if (!$manager) {
				$roleList[] = [
					'id' => DocumentPermission::MANAGER_PERMISSION,
					'name' => $roleList[DocumentPermission::MANAGER_PERMISSION]
				];
			} elseif ($manager->user_id == $params['user_id']) {
				continue;
			}

			$result['data'][] = [
				'id' => $row->id,
				'name' => $row->name,
				'description' => $row->descriptionShort,
				'is_public' => $row->isPublicDoc ,
				'cur_role' => $documentPermission ? $documentPermission->permission : 0,
				'role_list' => $roleList
			];
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
			'cover' => $document->cover,
			'description' => $document->description,
			'is_public' => $document->isPublicDoc,
			'login_preview' => $document->is_public == Document::LOGIN_PREVIEW_DOCUMENT,
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
		$operator = UserLogic::instance()->getByUid($uid);
		if (empty($operator)) {
			throw new ErrorHttpException('您操作的用户不存在');
		}

		$permission = intval($request->post('permission'));
		$documentId = intval($request->post('document_id'));

		/**
		 * permission 值不存在时，意味着删除权限
		 * 只要权限合适，减少判断直接删除
		 */
		if (empty($permission)) {
			$hasPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($documentId, $uid);
			if (!empty($hasPermission)) {
				$hasPermission->delete();
				UserOperateLog::query()->create([
					'user_id' => $user->id,
					'document_id' => $documentId,
					'chapter_id' => 0,
					'operate' => UserOperateLog::EDIT,
					'target_user_id' => $uid,
					'remark' => $user->username . '删除用户' . $operator->username . '的' . $hasPermission->aclName . '权限'
				]);
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

		$hasPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($documentId, $uid);
		if (empty($hasPermission)) {
			$hasPermission = new DocumentPermission();
			$hasPermission->user_id = $uid;
			$hasPermission->document_id = $documentId;
		}
		$hasPermission->permission = $permission;
		$hasPermission->save();

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $documentId,
			'chapter_id' => 0,
			'operate' => UserOperateLog::EDIT,
			'target_user_id' => $uid,
			'remark' => $user->username . '设置用户' . $operator->username . '为' . $hasPermission->aclName
		]);

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

		$document = Document::query()->create([
			'name' => trim($request->input('name')),
			'description' => trim($request->input('description')),
			'creator_id' => $user->id,
			'is_public' => intval($request->post('is_public')) ?? 1,
		]);

		DocumentLogic::instance()->createCreatorPermission($document);

		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $document->id,
			'chapter_id' => 0,
			'operate' => UserOperateLog::CREATE,
			'remark' => $user->username . '创建文档'
		]);

		return $this->data($document->id);
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
			$document->is_public = $request->input('login_preview') == 2 ? Document::LOGIN_PREVIEW_DOCUMENT : Document::PRIVATE_DOCUMENT;
		}

		$cover = $request->input('cover');
		if (isset($cover)) {
			$document->cover = $cover;
		}

		$document->save();

		if ($document->is_public == Document::PUBLIC_DOCUMENT) {
			DocumentPermission::query()->where('document_id', '=', $document->id)->where('permission', '=', DocumentPermission::READER_PERMISSION)->delete();
		}

		$user = $request->getAttribute('user');
		UserOperateLog::query()->create([
			'user_id' => $user->id,
			'document_id' => $document->id,
			'chapter_id' => 0,
			'operate' => UserOperateLog::EDIT,
			'remark' => $user->username . '编辑文档基本信息'
		]);

		return $this->data('success');
	}

	public function delete(Request $request)
	{
		$document = $this->checkPermissionAndGetDocument($request);
		try {
			$user = $request->getAttribute('user');
			DocumentLogic::instance()->deleteByDocument($document);

			UserOperateLog::query()->create([
				'user_id' => $user->id,
				'document_id' => $document->id,
				'chapter_id' => 0,
				'operate' => UserOperateLog::DELETE,
				'remark' => $user->username . '删除文档'
			]);
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

	public function changeDocumentFounder(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer',
			'username' => 'required',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}
		if (!$targetUser = UserLogic::instance()->getByUserName($params['username'])) {
			throw new ErrorHttpException('该用户不存在');
		}

		Document::query()->where('id', $params['document_id'])->update(['creator_id' => $targetUser->id]);
		//设置原管理员的权限为操作员
		$managerPermission = DocumentPermissionLogic::instance()->getByDocIdAndPermission($params['document_id'], DocumentPermission::MANAGER_PERMISSION);
		if ($managerPermission) {
			$managerPermission->permission = DocumentPermission::OPERATOR_PERMISSION;
			$managerPermission->save();
		}
		//删除该用户在源文档上的权限
		$originPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($params['document_id'], $targetUser->id);
		if ($originPermission) {
			$originPermission->delete();
		}
		//设置目标用户为管理员
		DocumentPermissionLogic::instance()->add($params['document_id'], $targetUser->id, DocumentPermission::MANAGER_PERMISSION);

		UserOperateLog::query()->create([
			'user_id' => !empty($managerPermission->user_id) ? $managerPermission->user_id : 0,
			'document_id' => $params['document_id'],
			'chapter_id' => 0,
			'target_user_id' => $targetUser->id,
			'operate' => UserOperateLog::DOCUMENT_TRANSFER,
			'remark' => $user->username . '转让文档到' . $targetUser->username
		]);

		return $this->data('success');
	}
}
