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

use W7\App\Model\Logic\SettingLogic;
use W7\Console\Command\CommandAbstract;

class CustomCommand extends CommandAbstract
{
	protected $description = '上线需要执行的脚本';

	protected function configure()
	{
		$this->setName('todo:custom');
	}

	protected function handle($options)
	{
		$this->vodInstall();
	}

	public function vodInstall()
	{
		$value = [
			'app_id' => '1253494855',
			'secret_id' => 'AKIDiGYibCWZNuA9rISLyKPCixvb092QXUAY',
			'secret_key' => '8zauxiDRO4UeX9VbQxDDvMWQ3PTGn3A7',
			'region' => 'ap-shanghai',
			'key' => 'R2kvrQHOu8ZyFANCpNpY',
		];
		SettingLogic::instance()->save(SettingLogic::KEY_VOD, $value);
	}
}
