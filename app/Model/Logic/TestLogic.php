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

use W7\App\Model\Entity\Test;
use W7\App\Model\Service\Api\TestApi;

class TestLogic extends BaseLogic
{
	public function addUser($name)
	{
		$user = Test::create(['name'=>$name]);
		return $user;
	}

	public function getUser($id)
	{
		$test = new TestApi();
		return $test->getChapter();
		$cacheUser = $this->get('user_'.$id);
		if ($cacheUser) {
			$user = $cacheUser;
			$user->from = 'cache';
		} else {
			$user = Test::find($id);
			if ($user) {
				$this->set('user_'.$id, $user);
			}
		}
		return $user;
	}
}
