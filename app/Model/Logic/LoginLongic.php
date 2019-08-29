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

class LoginLongic extends BaseLogic
{
	public function check($username,$userpass)
	{
		$userLogic = new UserLogic();
		$res = $userLogic->getUser(['username'=>$username]);
		if ($res) {
			$userpass = $userLogic->userpassEncryption($username, $userpass);
			if ($res['userpass'] == $userpass) {
				return ['code'=>1,'id'=>$res['id']];
			} else {
				return ['code'=>0,'msg'=>'用户名或者密码有误'];
			}
		} else {
			return ['code'=>0,'msg'=>'用户不存在'];
		}
	}
}
