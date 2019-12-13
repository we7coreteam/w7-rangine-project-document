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

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentPermission;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentPermissionLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getRoleList()
	{
		return (new DocumentPermission())->getRoleList();
	}

	public function add($documentId, $userId, $permission)
	{
		if (!Document::query()->find($documentId)) {
			throw new \RuntimeException('该文档不存在');
		}

		$documentPermission = new DocumentPermission();
		$documentPermission->user_id = $userId;
		$documentPermission->document_id = $documentId;
		$documentPermission->permission = $permission;

		if ($documentPermission->permission == DocumentPermission::MANAGER_PERMISSION) {
			//只能添加一个
			$exist = DocumentPermission::query()->where('document_id', '=', $documentId)->where('permission', '=', $permission)->first();
			if ($exist) {
				throw new \RuntimeException('该文档的管理员已存在');
			}
		}

		if (!$documentPermission->save()) {
			throw new \RuntimeException('文档权限添加失败');
		}

		return true;
	}

	private function updateByDocIdAndUid($documentId, $userId, $permission)
	{
		if (!Document::query()->find($documentId)) {
			throw new \RuntimeException('该文档不存在');
		}

		//如果当前权限是管理员，删除已存在的管理员
		if ($permission == DocumentPermission::MANAGER_PERMISSION) {
			$documentManager = DocumentPermission::query()->where('document_id', '=', $documentId)->where('permission', '', $permission)->first();
			if ($documentManager) {
				$documentManager->delete();
			}
		}

		$documentPermission = $this->getByDocIdAndUid($documentId, $userId);
		if ($documentPermission && !$permission) {
			$documentPermission->delete();
		}
		if (!$documentPermission) {
			$documentPermission = new DocumentPermission();
		}

		$documentPermission->user_id = $userId;
		$documentPermission->document_id = $documentId;
		$documentPermission->permission = $permission;

		if (!$documentPermission->save()) {
			throw new \RuntimeException('文档权限变更失败');
		}

		return true;
	}

	public function addByDocIds($userId, array $documentPermissions)
	{
		idb()->beginTransaction();
		try {
			foreach ($documentPermissions as $documentPermission) {
				$this->updateByDocIdAndUid($documentPermissions['document_id'], $userId, $documentPermission['permission']);
			}
			idb()->commit();
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw $e;
		}
	}

	public function addByDocIsPublic($userId, $isPublic, $permission)
	{
		idb()->beginTransaction();
		try {
			$documents = Document::query()->where('is_public', '=', $isPublic)->get()->toArray();
			foreach ($documents as $document) {
				$this->updateByDocIdAndUid($document['id'], $userId, $permission);
			}
			idb()->commit();
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw $e;
		}
	}

	public function getByDocIdAndUid($documentId, $userId)
	{
		return DocumentPermission::query()->where('document_id', '=', $documentId)->where('user_id', '=', $userId)->first();
	}

	/**
	 * 删除文档后，删除对应的权限
	 * @param $documentId
	 * @return bool
	 */
	public function clearByDocId($documentId)
	{
		return DocumentPermission::query()->where('document_id', '=', $documentId)->delete();
	}

	public function getFounderACL()
	{
		return [
			'name' => '创始人',
			'has_manage' => true,
			'has_edit' => true,
			'has_delete' => true,
			'has_read' => true,
		];
	}
}
