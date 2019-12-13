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

			$list = $query->paginate(self::PAGE_SIZE, '*', 'page', $page);

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
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档ID不能为空',
		]);

		if (!$request->getAttribute('user')->isManager) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$document = DocumentLogic::instance()->getById($params['document_id']);
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

		$operator = $document->operator()->with('user')->orderBy('permission')->get();
		if (!empty($operator)) {
			$operator->each(function ($row, $i) use (&$result) {
				$result['operator'][] = [
					'id' => $row->user->id,
					'username' => $row->user->username,
					'acl' => $row->acl
				];
			});
		}

		return $this->data($result);
	}

	public function operator(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('无编辑文档的权限');
		}

		$params = $this->validate($request, [
			'user_id' => 'required|integer',
			'document_id' => 'required|integer',
		], [
			'user_id.required' => '请指定用户',
			'document_id.required' => '请指定文档',
		]);

		$uid = intval($params['user_id']);
		$permission = intval($request->post('permission'));
		$documentId = intval($params['document_id']);

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
		$data = $this->validate($request, [
			'name' => 'required',
			'is_public' => 'required|in:' . Document::PUBLIC_DOCUMENT . ',' . Document::PRIVATE_DOCUMENT
		], [
			'name.required' => '文档名称不能为空',
		]);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$data['name'] = trim($data['name']);
		$data['creator_id'] = $user->id;
		$data['description'] = $request->input('description');

		try {
			$res = DocumentLogic::instance()->create($data);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function updateById(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('无编辑文档的权限');
		}

		$this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档ID不能为空',
		]);
		$documentId = $request->input('document_id');

		$data = [];
		if ($request->input('name') !== null) {
			$data['name'] = $request->input('name');
		}
		if ($request->input('description') !== null) {
			$data['description'] = $request->input('description');
		}
		if ($request->input('is_public') !== null) {
			$data['is_public'] = (int)$request->input('is_public');
			if (!in_array($data['is_public'], [Document::PUBLIC_DOCUMENT, Document::PRIVATE_DOCUMENT])) {
				throw new \RuntimeException('参数错误(is_public)');
			}
		}

		try {
			$res = DocumentLogic::instance()->updateById($documentId, $data);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function deleteById(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('无编辑文档的权限');
		}

		$this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档ID不能为空',
		]);
		$documentId = $request->input('document_id');

		try {
			$res = DocumentLogic::instance()->deleteById($documentId);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}
