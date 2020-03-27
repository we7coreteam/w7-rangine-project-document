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

use W7\App\Model\Entity\UserThirdParty;
use W7\Core\Helper\Traiter\InstanceTraiter;

class OauthLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getThirdPartyUserByUsernameUid($uid, $username)
	{
		return UserThirdParty::query()->where([
			'openid' => $uid,
			'username' => $username,
		])->first();
	}
}
