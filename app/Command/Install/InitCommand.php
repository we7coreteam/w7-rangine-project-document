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

namespace W7\App\Command\Install;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use W7\Console\Command\CommandAbstract;

class InitCommand extends CommandAbstract
{
	protected function configure()
	{
		$this
			// configure an argument
			->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
			// ...
		;
	}

	protected function handle($options)
	{
		// 生成.env配置文件

		// 导入SQL数据
//		$sql = file_get_contents(BASE_PATH . '/install/document.sql');
//		idb()->getPdo()->exec($sql);
		$this->output->writeln('SQL导入成功');
		$this->output->writeln('安装成功');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln([
			'User Creator',
			'============',
			'',
		]);

		$output->writeln('Username: ' . $input->getArgument('username'));
	}
}
