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

namespace W7\App\Command;

use Symfony\Component\Console\Input\InputOption;
use W7\Console\Command\CommandAbstract;

class ExampleCommand extends CommandAbstract
{
	protected function configure()
	{
		$this->addOption('--test', '-o', InputOption::VALUE_REQUIRED, 'the option desc');
	}

	protected function handle($options)
	{
		$this->output->writeln('the option test value is ' . $options['test'] ?? '');
		$this->output->writeln('process command');
	}
}
