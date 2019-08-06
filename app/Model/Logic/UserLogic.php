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

use W7\App\Model\Entity\User;

class UserLogic extends BaseLogic
{
	public function getUserlist($page)
	{
		$res = User::orderBy('id', 'desc')->get()->toArray();
		$this->doclogic = new DocumentLogic();
		return $this->doclogic->paging($this->handleUser($res),15,$page);
	}

	public function getUser($data)
	{
		if (isset($data['id']) && $data['id']) {
			$user = User::find($data['id']);
		}

		if (isset($data['username']) && $data['username']) {
			$user = User::where('username', $data['username'])->first();
		}

		return $user;
	}

	public function createUser($data)
	{
		$users = User::where('username', $data['username'])->count();

		if (!$users) {
			$res = User::create($data);
			$this->handleUser([$res]);
			return $res;
		}
		return false;
	}

	public function updateUser($id, $data)
	{
		return User::where('id', $id)->update($data);
	}

	public function delUser($ids)
	{
		$res = User::destroy($ids);
		if ($res) {
			foreach ($ids as $k => $v) {
				cache()->delete('username_'.$v);
			}
		}
		return $res;
	}

	public function searchUser($data,$page)
	{
		if (isset($data['username']) && $data['username']) {
			$res = User::select('id', 'username', 'has_privilege')
						->where('username', 'like', '%'.$data['username'].'%')
						->orderBy('id', 'desc')
						->get()
						->toArray();
			$this->doclogic = new DocumentLogic();
			return $this->doclogic->paging($this->handleUser($res),15,$page);
		}
	}

	public function hasDocuments($ids)
	{
		$this->docLogic = new DocumentLogic();

		$i = 0;
		foreach ($ids as $k => $val) {
			$res = $this->docLogic->getUserCreateDoc($val);
			if (!$res) {
				if ($this->delUser($val)) {
					$i++;
				}
			}
		}
		return ['msg'=>'成功删除'.$i.'用户，如果用户有文档不能直接删除'];
	}

	public function handleUser($res)
	{
		if (!$res) {
			return $res;
		}
		foreach ($res as $key => &$val) {
			if ($val['has_privilege'] == 1) {
				$val['has_privilege_name'] = '有';
			} else {
				$val['has_privilege_name'] = '无';
			}
			if ($val['userpass']) {
				unset($val['userpass']);
			}
		}
		return $res;
	}
}
