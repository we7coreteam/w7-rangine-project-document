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
use W7\App\Exception\ErrorHttpException;

class UserLogic extends BaseLogic
{
	use InstanceTraiter;

	const USER_LOGOUT_AFTER_CHANGE_PWD = 'user:logout:after:change:pwd:id:%s';

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

	public function createBucket($username, $avatar = '')
	{
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
			'avatar' => $avatar
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
			//修改完密码后强制退出
			icache()->delete(sprintf(self::USER_LOGOUT_AFTER_CHANGE_PWD, $userInfo['id']));
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

	public function follow($user_id, User $user)
	{
		$followUser = User::find($user_id);
		if (!$followUser) {
			throw new ErrorHttpException('此用户不存在');
		}
		if ($this->isFollowing($user_id, $user)) {
			throw new ErrorHttpException('您已关注此用户');
		}
		if ($user_id == $user->id) {
			throw new ErrorHttpException('不能关注自己');
		}
		$user->followings()->sync($user_id, false);
		return $followUser;
	}

	public function unFollow($user_id, User $user)
	{
		if (!$this->isFollowing($user_id, $user)) {
			throw new ErrorHttpException('您未关注此用户');
		}
		$user->followings()->detach($user_id, false);
		return User::find($user_id);
	}

	public function isFollowing($user_id, User $user)
	{
		return $user->followings->contains($user_id);
	}

	public function getFollowers($user_id, $login_user, $page = 1, $limit = 20)
	{
		$user = User::find($user_id);
		$followers = $user->followers()->orderBy('user_follower.created_at', 'desc')->paginate($limit, ['*'], 'page', $page);
		if ($login_user) {
			$loginUser = User::find($login_user['uid']);
			$followers->map(function ($item) use ($loginUser) {
				if ($loginUser->followings->contains($item->id)) {
					return $item->is_following = 1;
				} else {
					return $item->is_following = 0;
				}
			});
		}
		return $followers;
	}

	public function getFollowings($user_id, $login_user, $page = 1, $limit = 20)
	{
		$user = User::find($user_id);
		$followings = $user->followings()->orderBy('user_follower.created_at', 'desc')->paginate($limit, ['*'], 'page', $page);
		if ($login_user) {
			$loginUser = User::find($login_user['uid']);
			$followings->map(function ($item) use ($loginUser) {
				if ($loginUser->followings->contains($item->id)) {
					return $item->is_following = 1;
				} else {
					return $item->is_following = 0;
				}
			});
		}
		return $followings;
	}
}
