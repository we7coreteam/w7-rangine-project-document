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
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\PermissionDocument;
use W7\App\Model\Entity\User;

class DocumentLogic extends BaseLogic
{
	public function getlist($documents, $userId,$page)
	{
		if ($documents == 'all') {
			$res = Document::orderBy('updated_at', 'desc')->get()->toArray();
		} else {
			$res = Document::orderBy('updated_at', 'desc')->find($documents['document'])->toArray();
		}
		return $this->paging($this->handleDocumentRes($res, $userId),15,$page);
	}

	public function getDocUserList($id, $userId)
	{
		$documentUsers = PermissionDocument::where('document_id', $id)->pluck('user_id')->toArray();
		$res = User::select('id', 'username', 'has_privilege')->find($documentUsers);
		$res = $this->handleDocumentRes($res, $userId);
		if ($res) {
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

	public function getdetails($id, $userId)
	{
		$res = Document::find($id);
		$res = $this->handleDocumentRes([$res], $userId);
		return $res[0];
	}

	public function create($data)
	{
		return Document::create($data);
	}

	public function update($id, $data)
	{
		return Document::where('id', $id)->update($data);
	}

	public function del($id)
	{
		return Document::destroy($id);
	}

	public function search($name, $userId,$page)
	{
		$res = Document::where('name', 'like', '%'.$name.'%')->get()->toArray();
		return $this->paging($this->handleDocumentRes($res, $userId),15,$page);
	}

	public function relation($userId, $documentId)
	{
		$this->user = new UserLogic();
		$user = $this->user->getUser(['id'=>trim($userId)]);
		if ($user['has_privilege'] == 1) {
			return true;
		}
		$document = $this->getdetails($documentId, '', '');
		if (!$user) {
			return '用户不存在';
		}
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
		$this->user = new UserLogic();
		foreach ($res as $key => &$val) {
			if ($val['is_show'] == 1) {
				$val['is_show_name'] = '发布';
			} elseif ($res) {
				$val['is_show_name'] = '隐藏';
			}

			if ($val['creator_id']) {
				$user = $this->user->getUser(['id'=>trim($val['creator_id'])]);
				if ($user) {
					$val['username'] = $user['username'];
				} else {
					$val['username'] = '';
				}
			}
			if ($val['has_privilege'] == 1) {
				$val['has_creator'] = 1;
				$val['has_creator_name'] = '管理员';
			} else {
				if ($userId) {
					if ($val['creator_id'] == $userId) {
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

	public function getShowList($keyword,$page)
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
		return $this->paging($this->handleDocumentRes($res, ''),15,$page);
	}

	public function paging($data,$perPage,$page)
	{
		$perPage = $perPage <= 0 ? 15 : $perPage;
		if ($page) {
			$current_page = $page;
			$current_page = $current_page <= 0 ? 1 :$current_page;
		} else {
			$current_page = 1;
		}
		$item = array_slice($data, ($current_page-1)*$perPage, $perPage);
		$total = count($data);

		$paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
			'path' => Paginator::resolveCurrentPath(),
			'pageName' => 'page',
		]);

		return [
			'total' => $total,
			'pageCount' => ceil($total/$perPage),
			'data' => $paginator->toArray()['data']
		];
	}

}
