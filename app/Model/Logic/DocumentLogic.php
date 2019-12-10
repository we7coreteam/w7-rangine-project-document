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
use W7\App;
use W7\App\Event\ChangeAuthEvent;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\PermissionDocument;
use W7\App\Model\Entity\User;
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



	public function getlist($page, $name)
	{
		$request = App::getApp()->getContext()->getRequest();
		$documents = $request->document_user_auth;
		$userId = $request->document_user_id;
		if ($documents == 'all') {
			$res = Document::query()->where('name', 'like', '%'.$name.'%')->with('user')->orderBy('updated_at', 'desc')->get()->toArray();
		} else {
			$res = Document::query()->where('name', 'like', '%'.$name.'%')->with('user')->orderBy('updated_at', 'desc')->find($documents)->toArray();
		}
		return $this->paging($this->handleDocumentRes($res, $userId), 15, $page);
	}

	public function getDocUserList($id)
	{
		$request = App::getApp()->getContext()->getRequest();
		$userId = $request->document_user_id;
		$res = '';
		$document = Document::find($id);

		if (!$document) {
			return $res;
		}
		$documentUsers = PermissionDocument::where('document_id', $id)->pluck('user_id')->toArray();
		if (!$documentUsers) {
			return $res;
		}
		$res = User::select('id', 'username', 'has_privilege')->find($documentUsers);
		if ($res) {
			$res = $this->handleDocumentRes($res, $userId);
			foreach ($res as $k => &$v) {
				if ($v['has_privilege'] || $v['has_privilege'] == 0) {
					unset($v['has_privilege']);
				}
				if ($v['is_show_name']) {
					unset($v['is_show_name']);
				}
			}
		}
		return $res;
	}

	public function getdetails($id)
	{
		$request = App::getApp()->getContext()->getRequest();
		$userId = $request->document_user_id;
		$res = Document::find($id);
		if ($res) {
			$res = $this->handleDocumentRes([$res], $userId);
			return $res[0];
		}
		return $res;
	}

	public function details($id)
	{
		return Document::find($id);
	}

	public function create($data)
	{
		$res = Document::create($data);
		if ($res) {
			PermissionDocument::create(['user_id' => $data['creator_id'],'document_id' => $res['id']]);
			ChangeAuthEvent::instance()->attach('user_id', $data['creator_id'])->attach('document_id', $res['id'])->dispatch();
		}
		return $res;
	}

	public function update($id, $data)
	{
		return Document::where('id', $id)->update($data);
	}

	public function del($id)
	{
		idb()->beginTransaction();
		$res = Document::destroy($id);
		if ($res) {
			$chapter = new ChapterLogic();
			$chapter->deleteDocument($id);
			ChangeAuthEvent::instance()->attach('user_id', 0)->attach('document_id', $res['id'])->dispatch();
			idb()->commit();
		} else {
			idb()->rollBack();
		}
		return $res;
	}

	public function relation($documentId)
	{
		$request = App::getApp()->getContext()->getRequest();
		$userId = $request->document_user_id;
		$user = new UserLogic();
		$user = $user->getUser(['id' => $userId]);
		if ($user['has_privilege'] == 1) {
			return true;
		}
		if (!$user) {
			return '用户不存在';
		}
		$document = $this->getdetails($documentId);
		if (!$document) {
			return '文档不存在';
		}
		if ($user['id'] != $document['creator_id']) {
			return '只有文档创建者才可以操作';
		}
		return true;
	}

	public function handleDocumentRes($res, $userId)
	{
		if (!$res) {
			return $res;
		}

		foreach ($res as $key => &$val) {
			if (isset($val['is_show']) && $val['is_show'] == 1) {
				$val['is_show_name'] = '发布';
			} elseif ($res) {
				$val['is_show_name'] = '隐藏';
			}

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
