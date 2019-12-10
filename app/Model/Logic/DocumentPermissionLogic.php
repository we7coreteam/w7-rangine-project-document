<?php

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentPermission;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentPermissionLogic extends BaseLogic {
	use InstanceTraiter;

	public function add($documentId, $userId, $permission) {
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

	public function getByDocIdAndUid($documentId, $userId) {
		return DocumentPermission::query()->where('document_id', '=', $documentId)->where('user_id', '=', $userId)->first();
	}

	/**
	 * 获取文档的所有权限用户
	 * @param $documentId
	 * @return array
	 */
	public function getListByDocId($documentId) {
		$documentPermissions = DocumentPermission::query()->where('document_id', '=', $documentId)->get();
		/**
		 * @var DocumentPermission $documentPermission
		 */
		$result = [];
		foreach ($documentPermissions as $documentPermission) {
			$result[] = [
				'id' => $documentPermission->id,
				'user_name' => $documentPermission->user->username,
				'user_role' => $documentPermission->permission,
				'permission' => [
					'has_delete' => $documentPermission->hasManage(),
					'has_edit' => $documentPermission->hasManage(),
					'has_manage' => $documentPermission->hasManage()
				]
			];
		}

		return $result;
	}

	public function updatePermissionById($id, $permission) {
		/**
		 * @var DocumentPermission $documentPermission
		 */
		$documentPermission = DocumentPermission::query()->where('id', '=', $id)->first();
		if (!$documentPermission) {
			throw new \RuntimeException('该文档权限不存在');
		}

		$documentPermission->permission = $permission;
		if (!$documentPermission->save()) {
			throw new \RuntimeException('权限更新失败');
		}

		return true;
	}

	public function deleteById($id) {
		$deleted = DocumentPermission::query()->where('id', '=', $id)->delete();
		if (!$deleted) {
			throw new \RuntimeException('文档权限删除失败');
		}

		return true;
	}

	/**
	 * 删除文档后，删除对应的权限
	 * @param $documentId
	 * @return bool
	 */
	public function clearByDocId($documentId) {
		$deleted = DocumentPermission::query()->where('document_id', '=', $documentId)->delete();
		if (!$deleted) {
			throw new \RuntimeException('文档权限清除失败');
		}

		return true;
	}
}