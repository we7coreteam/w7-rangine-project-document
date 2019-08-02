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
use W7\App\Model\Entity\User;

class DocumentLogic extends BaseLogic
{
	public function getlist($documents, $userId)
	{
		if ($documents == 'all') {
			$res = Document::orderBy('updated_at', 'desc')->get();
		} else {
			$res = Document::orderBy('updated_at', 'desc')->find($documents);
		}
		return $this->handleDocumentRes($res, $userId);
	}

	public function getDocumentUsers($id)
	{
		return User::find($id);
	}

	public function getdetails($id)
	{
		$res = Document::find($id);
		if ($res && $res['is_show'] == 1) {
			$res['is_show'] = '显示';
		} elseif ($res && $res['is_show'] == 0) {
			$res['is_show'] = '隐藏';
		}
		if ($res && $res['creator_id']) {
			$this->user = new UserLogic();
			$user = $this->user->getUser(['id'=>trim($res['creator_id'])]);
			if ($user) {
				$res['username'] = $user['username'];
			}
		}

		return $res;
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

	public function search($name)
	{
		return Document::where('name', 'like', '%'.$name.'%')->get();
	}

	public function relation($username, $documentId)
	{
		$this->user = new UserLogic();
		$user = $this->user->getUser(['username'=>trim($username)]);
		if ($user['has_privilege'] == 1) {
			return true;
		}
		$document = $this->getdetails($documentId);
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
				$val['is_show'] = '显示';
			} elseif ($res && $res['is_show'] == 0) {
				$val['is_show'] = '隐藏';
			}

			if ($val['has_privilege'] == 1) {
				$val['has_privilege'] = '有';
			} else {
				$val['has_privilege'] = '无';
			}

			if ($val['creator_id']) {
				$user = $this->user->getUser(['id'=>trim($val['creator_id'])]);
				if ($user) {
					$val['username'] = $user['username'];
				}
			}
			if ($val['creator_id'] == $userId) {
				$val['has_creator'] = '创建者';
			} else {
				$val['has_creator'] = '参与者';
			}
		}
		return $res;
	}

	public function getUserCreateDoc($id)
	{
		return Document::where('creator_id', $id)->first();
	}

	public function getShowList($keyword)
	{
		if ($keyword) {
			$res = Document::where('name', 'like', '%'.$keyword['name'].'%')
						->where('is_show', 1)
						->orderBy('updated_at', 'desc')
						->get();
		} else {
			$res = Document::where('is_show', 1)
						->orderBy('updated_at', 'desc')
						->get();
		}
		return $this->handleDocumentRes($res, '');
	}

	public function test()
	{
		$this->test = new UserAuthorizationLogic();
		return $this->test->getUserAuthorizations(2);
	}
}
