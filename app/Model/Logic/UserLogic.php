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
use W7\Core\Helper\Traiter\InstanceTraiter;

class UserLogic extends BaseLogic
{
	use InstanceTraiter;

	/**
	 * 根据用户名获取用户
	 * @param $username
	 * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
	 */
	public function getByUserName($username)
	{
		return User::query()->where('username', $username)->first();
	}

	public function getByUid($uid)
	{
		return User::query()->where('id', $uid)->first();
	}

	public function createUser($data)
	{
		$user = $this->getByUserName($data['username']);
		if ($user) {
			throw new \RuntimeException('用户名已经存在');
		}

		$data['userpass'] = $this->userPwdEncryption($data['username'], $data['userpass']);
		$user = User::query()->create($data);
		if (!$user) {
			throw new \RuntimeException('用户添加失败');
		}

		return $user->id;
	}


	public function createBucket($username) {
		$user = $this->getByUserName($username);
		if ($user) {
			return $user->id;
		}

		$user = User::query()->create([
			'username' => $username,
			'userpass' => '',
			'remark' => '',
			'is_ban' => 0,
			'group_id' => 0,
		]);

		if (!$user) {
			throw new \RuntimeException('用户添加失败');
		}

		return $user->id;
	}

	public function updateUser($userInfo)
	{
		$user = $this->getByUserName($userInfo['username']);
		if ($user && $userInfo['id'] != $user->id) {
			throw new \RuntimeException('用户名已经存在');
		}

		if (!empty($userInfo['userpass'])) {
			$userInfo['userpass'] = $this->userPwdEncryption($userInfo['username'], $userInfo['userpass']);
		}
		
		$result = User::query()->where('id', $userInfo['id'])->update($userInfo);
		if (!$result) {
			throw new \RuntimeException('修改用户信息失败');
		}

		return $result;
	}

	public function deleteByIds(array $ids)
	{
		$docLogic = new DocumentLogic();

		$delNum = 0;
		foreach ($ids as $k => $val) {
			$res = $docLogic->getDocByCreatorId($val);
			if (!$res) {
				/**
				 * @var User $user
				 */
				$user = $this->getByUid($val);
				if ($user && !$user->isFounder) {
					if (User::destroy($val)) {
						DocumentPermissionLogic::instance()->clearByUid($val);
						StarLogic::instance()->clearByUid($val);
						UserOperateLogic::instance()->clearByUid($val);
						$delNum++;
					}
				}
			}
		}

		return $delNum;
	}

	public function userPwdEncryption($username, $userpass)
	{
		return md5(md5($username.$userpass));
	}
}
