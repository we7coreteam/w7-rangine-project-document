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
use W7\App\Model\Entity\PermissionDocument;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getById($id)
	{
		$id = intval($id);
		if (empty($id)) {
			return [];
		}
		return Document::query()->find($id);
	}

	public function deleteById($documentId) {
		$documentId = intval($documentId);
		if (empty($documentId)) {
			return true;
		}
		$document = $this->getById($documentId);
		if (empty($document)) {
			return true;
		}
		return $this->deleteByDocument($document);
	}

	public function deleteByDocument(Document $document) {
		if (!$document->delete()) {
			throw new \RuntimeException('文档删除失败，请重试');
		}
		//删除权限
		DocumentPermissionLogic::instance()->clearByDocId($document->id);

		//删除章节及文章
		ChapterLogic::instance()->deleteByDocumentId($document->id);

		return true;
	}

	public function createCreatorPermission(Document $document) {
		DocumentPermission::query()->create([
			'document_id' => $document->id,
			'user_id' => $document->creator_id,
			'permission' => DocumentPermission::MANAGER_PERMISSION,
		]);
		return true;
	}

	public function getUserCreateDoc($id)
	{
		return Document::where('creator_id', $id)->first();
	}

	public function getShowList($keyword, $page)
	{
		if ($keyword) {
			$res = Document::where('name', 'like', '%'.$keyword['name'].'%')
						->where('is_show', 1)
						->orderBy('updated_at', 'desc')
						->get()->toArray();
		} else {
			$res = Document::where('is_show', 1)
						->orderBy('updated_at', 'desc')
						->get()->toArray();
		}
		return $this->paging($this->handleDocumentRes($res, ''), 15, $page);
	}
}
