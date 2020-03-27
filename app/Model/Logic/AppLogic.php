<?php

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\App;

class AppLogic extends BaseLogic
{
	public function getByAppId($appId)
	{
		if (empty($appId)) {
			throw new \RuntimeException('Invalid appid');
		}
		return App::where('appid', '=', $appId)->first();
	}

	public function getSign($data, $token = '')
	{
		unset($data['sign']);

		ksort($data, SORT_STRING);
		reset($data);

		$sign = md5(http_build_query($data, '', '&') . $token);
		return $sign;
	}
}
