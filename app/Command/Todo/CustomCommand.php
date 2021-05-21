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

namespace W7\App\Command\Todo;

use W7\Console\Command\CommandAbstract;
use W7\App\Model\Entity\User;

class CustomCommand extends CommandAbstract
{
	protected $description = '上线需要执行的脚本';

	protected function configure()
	{
		$this->setName('todo:custom');
	}

	protected function handle($options)
	{
		go(function () {
			$users = User::whereIn('username', function ($query) {
				$query->select('username')->from('user')->groupBy('username')->havingRaw('COUNT(username) > 1');
			})->get();
			$users->map(function ($item) {
				$item->username = $item->username . Str::random(6);
				$item->save();
			});
			$this->output->success('处理成功');
		});
	}
}
