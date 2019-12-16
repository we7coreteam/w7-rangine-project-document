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

	public function deleteByDocument(Document $document)
	{
		if (!$document->delete()) {
			throw new \RuntimeException('文档删除失败，请重试');
		}
		//删除权限
		DocumentPermissionLogic::instance()->clearByDocId($document->id);

		//删除章节及文章
		ChapterLogic::instance()->deleteByDocumentId($document->id);

		return true;
	}

	public function createCreatorPermission(Document $document)
	{
		DocumentPermission::query()->create([
			'document_id' => $document->id,
			'user_id' => $document->creator_id,
			'permission' => DocumentPermission::MANAGER_PERMISSION,
		]);
		return true;
	}

	public function getDocByCreatorId($id)
	{
		return Document::query()->where('creator_id', $id)->first();
	}

	/**
	 * @deprecated
	 * @param $keyword
	 * @param $page
	 * @return array
	 */
	public function getShowList($keyword, $page)
	{
		$query = Document::query()->where('is_public', 1)->orderBy('updated_at', 'desc');
		if (!empty($keyword['name'])) {
			$query = $query->where('name', 'like', '%'.$keyword['name'].'%');
		}
		$list = $query->paginate(null, '*', 'page', $page);

		return [
			'total' => $list->total(),
			'pageCount' => $list->lastPage(),
			'data' => $list->items()
		];
	}
}
