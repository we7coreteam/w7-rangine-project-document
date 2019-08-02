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
	public function getUserlist()
	{
		return User::orderBy('id', 'desc')->get();
	}

	public function getUser($data)
	{
		if ($data['id']) {
			$user = User::find($data['id']);
		}

		if ($data['username']) {
			$user = User::where('username', $data['username'])->first();
		}

		return $user;
	}

	public function createUser($data)
	{
		$users = User::where('username', $data['username'])->count();

		if (!$users) {
			return User::create($data);
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
				icache()->delete('username_'.$v);
			}
		}
		return $res;
	}

	public function searchUser($data)
	{
		if (isset($data['username'])) {
			$res = User::select('id', 'username', 'has_privilege')->where('username', 'like', '%'.$data['username'].'%')->get();
			if ($res) {
				foreach ($res as $key => &$val) {
					if ($val['has_privilege'] == 1) {
						$val['has_privilege'] = '有';
					} else {
						$val['has_privilege'] = '无';
					}
				}
			}
			return $res;
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
		return '成功删除'.$i.'用户，其他用户有文档不能直接删除';
	}
}
