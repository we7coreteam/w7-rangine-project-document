<?php

namespace W7\Console\Command\Config;

use Symfony\Component\Console\Input\InputOption;
use W7\Console\Command\CommandAbstract;
use W7\Core\Exception\CommandException;

class ListCommand extends CommandAbstract {
	protected $description = 'gets user configuration information';

	protected function configure() {
		$this->addOption('--search', '-s', InputOption::VALUE_REQUIRED, 'configuration to search for, for example:  app.database.default');
	}

	protected function handle($options) {
		if (empty($options['search'])) {
			throw new CommandException('the option search not be empty');
		}

		$options = explode('.', $options['search']);
		$config = iconfig()->getUserConfig($options[0]);
		array_shift($options);

		$config = $this->getData($options, $config);

		$this->output->writeList($this->formatData($config, $options['search']));
	}

	private function getData($options, $config) {
		foreach ($options as $item) {
			if (empty($config[$item])) {
				return [];
			}
			$config = $config[$item];
		}
		return $config;
	}

	private function formatData($config, $key) {
		return [
			'your ' . $key. ' config:' => $config
		];
	}
}