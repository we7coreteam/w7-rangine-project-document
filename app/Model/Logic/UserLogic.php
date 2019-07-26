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

		if ($data['phone']) {
			$user = User::where('phone', $data['phone'])->first();
		}

		return $user;
	}

	public function getUserDocList($documents)
	{
		if ($documents == 'all'){
			$res = Document::orderBy('updated_at','desc')->get();
		}else{
			$res = Document::orderBy('updated_at','desc')->find($documents);
		}
		$this->docLogic = new DocumentLogic();
		return $this->docLogic->handleDocumentRes($res);
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
		return User::destroy($ids);
	}

	public function searchUser($data)
	{
		if (isset($data['username'])) {
			return User::where('username', 'like', '%'.$data['username'].'%')->get();
		}
	}
}
