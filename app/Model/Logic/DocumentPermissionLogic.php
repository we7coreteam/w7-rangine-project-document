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
		return (new DocumentPermission())->roleList;
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

		//如果permission为空，删除对应权限
		$documentPermission = $this->getByDocIdAndUid($documentId, $userId);
		if (!$permission) {
			$documentPermission && $documentPermission->delete();
			return true;
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
				$this->updateByDocIdAndUid($documentPermission['document_id'], $userId, $documentPermission['permission']);
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
