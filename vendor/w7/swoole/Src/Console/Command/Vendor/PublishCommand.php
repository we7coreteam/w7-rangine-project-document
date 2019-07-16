<?php

namespace W7\Console\Command\Vendor;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use W7\Console\Command\CommandAbstract;
use W7\Core\Exception\CommandException;
use W7\Core\Provider\ProviderAbstract;

class PublishCommand extends CommandAbstract {
	protected $description = 'publish the vendor configuration to the app';
	private $fileSystem;

	protected function configure() {
		$this->addOption('--provider', '-p', InputOption::VALUE_REQUIRED, 'the class name of the extension package, including namespace');
		$this->addOption('--tag', '-t', InputOption::VALUE_REQUIRED, 'tag of extension packages');
		$this->addOption('--force', '-f', null, 'mandatory coverage configuration');
		$this->fileSystem = new Filesystem();
	}

	protected function handle($options) {
		if (empty($options['provider']) && empty($options['tag'])) {
			throw new CommandException('option provider or tag not be empty');
		}

		$this->publishTag($options['provider'] ?? '', $options['tag'] ?? '');

		$this->output->info('Publishing complete.');
	}

	/**
	 * Publishes the assets for a tag.
	 *
	 * @param  string  $tag
	 * @return mixed
	 */
	private function publishTag($provider, $tag) {
		foreach ($this->pathsToPublish($provider, $tag) as $from => $to) {
			$this->publishItem($from, $to);
		}
	}

	/**
	 * Get all of the paths to publish.
	 *
	 * @param  string  $tag
	 * @return array
	 */
	private function pathsToPublish($provider, $tag) {
		return ProviderAbstract::pathsToPublish($provider, $tag);
	}

	/**
	 * Publish the given item from and to the given location.
	 *
	 * @param  string  $from
	 * @param  string  $to
	 * @return void
	 */
	private function publishItem($from, $to) {
		if ($this->fileSystem->isFile($from)) {
			return $this->publishFile($from, $to);
		} else if ($this->fileSystem->isDirectory($from)) {
			return $this->publishDirectory($from, $to);
		}

		$this->output->error("Can't locate path: <{$from}>");
	}

	/**
	 * Publish the file to the given path.
	 *
	 * @param  string  $from
	 * @param  string  $to
	 * @return void
	 */
	private function publishFile($from, $to) {
		if (!$this->fileSystem->exists($to) || $this->input->getOption('force')) {
			$this->createParentDirectory(dirname($to));
			$this->fileSystem->copy($from, $to);
			$this->status($from, $to, 'File');
		}
	}

	/**
	 * Publish the directory to the given directory.
	 *
	 * @param  string  $from
	 * @param  string  $to
	 * @return void
	 */
	private function publishDirectory($from, $to) {
		$this->fileSystem->copyDirectory($from, $to);

		$this->status($from, $to, 'Directory');
	}

	/**
	 * Create the directory to house the published files if needed.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	private function createParentDirectory($directory) {
		if (!$this->fileSystem->isDirectory($directory)) {
			$this->fileSystem->makeDirectory($directory, 0755, true);
		}
	}

	/**
	 * Write a status message to the console.
	 *
	 * @param  string  $from
	 * @param  string  $to
	 * @param  string  $type
	 * @return void
	 */
	private function status($from, $to, $type) {
		$from = str_replace(BASE_PATH, '', realpath($from));
		$to = str_replace(BASE_PATH, '', realpath($to));
		$this->output->writeln('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
	}
}