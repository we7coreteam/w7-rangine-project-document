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

use W7\App\Event\ChangeAuthEvent;
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
	public function getByUsername($username)
	{
		if (empty($username)) {
			return [];
		}
		$user = User::query()->where('username', $username)->first();
		return $user;
	}

	public function getByUid($uid)
	{
		$uid = intval($uid);
		if (empty($uid)) {
			return [];
		}
		$user = User::query()->where('id', $uid)->first();
		return $user;
	}

	/**
	 * 将用户提交的密码转化为数据库存储密码
	 * @param User $user
	 * @param $postPassword
	 * @return string
	 */
	public function getPasswordEncryption(User $user, $postPassword)
	{
		return md5(md5($user->username . $postPassword));
	}

	public function getUser($data)
	{
		if (isset($data['id']) && $data['id']) {
			return User::query()->find($data['id']);
		}

		if (isset($data['username']) && $data['username']) {
			return User::query()->where('username', $data['username'])->first();
		}

		return '';
	}

	public function createUser($data)
	{
		$users = User::query()->where('username', $data['username'])->count();

		if (!$users) {
			$data['userpass'] = $this->userpassEncryption($data['username'], $data['userpass']);
			return User::query()->create($data);
		}
		return false;
	}

	public function updateUser($id, $user)
	{
		$userInfo = $this->getUser(['username' => $user['username']]);
		if ($userInfo && $id != $userInfo->id) {
			throw new \RuntimeException('用户名已经存在');
		}

		$user['userpass'] = $this->userpassEncryption($user['username'], $user['userpass']);
		ChangeAuthEvent::instance()->attach('user_id', $id)->attach('document_id', 0)->dispatch();
		$result = User::query()->where('id', $id)->update($user);
		if (!$result) {
			throw new \RuntimeException('修改用户信息失败');
		}

		return $result;
	}

	public function detailById($id)
	{
		$res = User::query()->find($id);
		if ($res) {
			$res = $this->handleUser([$res]);
			return $res[0];
		}
		return $res;
	}

	public function delUser($ids)
	{
		return User::destroy($ids);
	}

	public function deleteUsers($ids)
	{
		$doclogic = new DocumentLogic();

		$i = 0;
		foreach ($ids as $k => $val) {
			$res = $doclogic->getUserCreateDoc($val);
			if (!$res) {
				$user = User::find($val);
				if ($user && $user['has_privilege'] != 1) {
					if ($this->delUser($val)) {
						$i++;
					}
				}
			}
		}

		return ['msg'=> '成功删除' . $i . '用户，如果用户有文档不能直接删除'];
	}

	public function handleUser($res)
	{
		if (!$res) {
			return $res;
		}
		foreach ($res as $key => &$val) {
			if (isset($val['has_privilege']) && $val['has_privilege'] == 1) {
				$val['has_privilege_name'] = '有';
			} else {
				$val['has_privilege_name'] = '无';
			}
			if (isset($val['userpass']) && $val['userpass']) {
				unset($val['userpass']);
			}
		}
		return $res;
	}

	public function userpassEncryption($username, $userpass)
	{
		return md5(md5($username.$userpass));
	}

	public function checkUsername($id, $postId)
	{
		if ($id != $postId) {
			throw new \Exception('用户名已经存在');
		}
	}
}
