<?php

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentPermission;
use W7\Core\Database\LogicAbstract;

class DocumentPermissionLogic extends LogicAbstract {
	public function add($documentId, $userId, $permission) {
		if (!Document::query()->find($documentId)) {
			throw new \RuntimeException('该文档不存在');
		}

		$documentPermission = new DocumentPermission();
		$documentPermission->user_id = $userId;
		$documentPermission->document_id = $documentId;
		$documentPermission->permission = $permission;

		if ($documentPermission->isManager()) {
			//只能添加一个
			$exist = DocumentPermission::where('document_id', '=', $documentId)->where('permission', '=', $permission)->first();
			if ($exist) {
				throw new \RuntimeException('该文档的管理员已存在');
			}
		}

		if (!$documentPermission->save()) {
			throw new \RuntimeException('文档权限添加失败');
		}

		return true;
	}

	public function list($documentId) {
		$documentPermissions = (new DocumentPermission())->where('document_id', '=', $documentId)->where('document_id', '=', $documentId)->get();
		if (!$documentPermissions) {

		}

	}

	public function update($id, $permission) {
		/**
		 * @var DocumentPermission $documentPermission
		 */
		$documentPermission = DocumentPermission::where('id', '=', $id)->first();
		if (!$documentPermission) {
			throw new \RuntimeException('该文档权限不存在');
		}

		$documentPermission->permission = $permission;
		if (!$documentPermission->save()) {
			throw new \RuntimeException('权限更新失败');
		}

		return true;
	}

	public function delete($id) {
		$deleted = DocumentPermission::where('id', '=', $id)->delete();
		if (!$deleted) {
			throw new \RuntimeException('文档权限删除失败');
		}

		return true;
	}

	public function clear($documentId) {
		$deleted = DocumentPermission::where('document_id', '=', $documentId)->delete();
		if (!$deleted) {
			throw new \RuntimeException('文档权限清除失败');
		}

		return true;
	}
}