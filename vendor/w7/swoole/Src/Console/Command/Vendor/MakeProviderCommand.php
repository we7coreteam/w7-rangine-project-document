<?php

namespace W7\Console\Command\Vendor;

use W7\Console\Command\GeneratorCommandAbstract;

class MakeProviderCommand extends GeneratorCommandAbstract {
	protected $description = 'generate provider';

	protected function before() {
		$this->name = ucfirst($this->name);
		if ($this->filesystem->exists($this->rootPath() . $this->name . '.php')) {
			throw new \Exception('the provider ' . $this->name . ' is existed');
		}
	}

	protected function getStub() {
		return dirname(__DIR__, 1).'/Stubs/provider.stub';
	}

	protected function replaceStub() {
		$stubFile = $this->name . '.stub';
		$this->replace('{{ DummyNamespace }}',  'W7\App\Provider', $stubFile );
		$this->replace('{{ DummyClass }}', $this->name, $stubFile);
	}

	protected function savePath() {
		return 'app/Provider';
	}
}