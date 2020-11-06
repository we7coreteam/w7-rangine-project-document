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

class CustomCommand extends CommandAbstract
{
	protected $description = '上线需要执行的脚本';

	protected function configure()
	{
		$this->setName('todo:custom');
	}

	protected function handle($options)
	{
	}
}
