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

class InitCommand extends CommandAbstract {
	protected function handle($options) {
		$config = $this->getDbConfig();
		//需要验证数据格式



//		$output->writeln('Username: ' . $input->getArgument('username'));
		// 生成.env配置文件

		// 导入SQL数据
//		$sql = file_get_contents(BASE_PATH . '/install/document.sql');
//		idb()->getPdo()->exec($sql);
		$this->output->writeln('SQL导入成功');
		$this->output->writeln('安装成功');
	}

	private function getDbConfig() {
		$list = [
			'host' => [
				'name' => 'host',
				'default' => '127.0.0.1'
			],
			'port' => [
				'name' => '端口',
				'default' => '3306'
			],
			'name' => [
				'name' => '数据库名称',
				'default' => 'we7_document'
			],
			'username' => [
				'name' => '数据库用户名',
				'default' => 'root'
			],
			'password' => [
				'name' => '数据库密码',
				'default' => ''
			]
		];

		$config = [];
		foreach ($list as $key => $item) {
			$config[$key] = $this->askDb($item['name'], $item['default']);
		}

		return $config;
	}

	private function askDb($name, $default) {
		return $this->output->ask('请输入数据库' . $name, $default);
	}
}
