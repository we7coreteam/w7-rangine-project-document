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

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use W7\App\Event\ChangeAuthEvent;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentPermission;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getById($id)
	{
		return Document::query()->find($id);
	}

	public function create($data)
	{
		try {
			idb()->beginTransaction();
			$res = Document::query()->create($data);
			if ($res) {
				DocumentPermissionLogic::instance()->add($res['id'], $data['creator_id'], DocumentPermission::MANAGER_PERMISSION);
				ChangeAuthEvent::instance()->attach('user_id', $data['creator_id'])->attach('document_id', $res['id'])->dispatch();
				idb()->commit();
				return true;
			}
			throw new \RuntimeException('新建文档失败');
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw $e;
		}
	}

	public function updateById($id, $data)
	{
		if (!Document::query()->where('id', $id)->update($data)) {
			throw new \RuntimeException('文档编辑失败');
		}
	}

	public function deleteById($id)
	{
		try {
			idb()->beginTransaction();
			$deleted = Document::query()->where('id', '=', $id)->delete();
			if ($deleted) {
				ChapterLogic::instance()->deleteDocument($id);
				DocumentPermissionLogic::instance()->clearByDocId($id);
				ChangeAuthEvent::instance()->attach('user_id', 0)->attach('document_id', $id)->dispatch();
				idb()->commit();
				return true;
			}

			throw new \RuntimeException('删除文档失败');
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw $e;
		}
	}

	public function handleDocumentRes($res, $userId)
	{
		if (!$res) {
			return $res;
		}

		foreach ($res as $key => &$val) {
			if (isset($val['user']) && $val['user'] && is_array($val['user'])) {
				$val['username'] = $val['user']['username'];
			}
			unset($val['user']);

			if (isset($val['has_privilege']) && $val['has_privilege'] == 1) {
				$val['has_creator'] = 1;
				$val['has_creator_name'] = '管理员';
			} else {
				if ($userId) {
					if ($userId == 1) {
						$val['has_creator'] = 1;
						$val['has_creator_name'] = '管理员';
					} elseif ($val['creator_id'] == $userId) {
						$val['has_creator'] = 2;
						$val['has_creator_name'] = '创建者';
					} else {
						$val['has_creator'] = 3;
						$val['has_creator_name'] = '操作员';
					}
				}
			}
		}
		return $res;
	}

	public function getDocByCreatorId($id)
	{
		return Document::query()->where('creator_id', $id)->first();
	}

	public function getShowList($keyword, $page)
	{
		if ($keyword) {
			$res = Document::where('name', 'like', '%'.$keyword['name'].'%')
						->where('is_public', 1)
						->orderBy('updated_at', 'desc')
						->get()->toArray();
		} else {
			$res = Document::where('is_public', 1)
						->orderBy('updated_at', 'desc')
						->get()->toArray();
		}
		return $this->paging($this->handleDocumentRes($res, ''), 15, $page);
	}

	/**
	 * @param $data
	 * @param $perPage
	 * @param $page
	 * @deprecated
	 * @return array
	 */
	public function paging($data, $perPage, $page)
	{
		$perPage = $perPage <= 0 ? 15 : $perPage;
		if ($page) {
			$current_page = $page;
			$current_page = $current_page <= 0 ? 1 : $current_page;
		} else {
			$current_page = 1;
		}
		$item = array_slice($data, ($current_page - 1) * $perPage, $perPage);
		$total = count($data);

		$paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
			'path' => Paginator::resolveCurrentPath(),
			'pageName' => 'page',
		]);

		return [
			'total' => $total,
			'pageCount' => ceil($total / $perPage),
			'data' => $paginator->toArray()['data']
		];
	}
}
