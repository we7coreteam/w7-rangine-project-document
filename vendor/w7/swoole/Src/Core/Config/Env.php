<?php
/**
 * 根据当前Hostname获取env值
 * @author donknap
 * @date 19-4-9 上午10:21
 */

namespace W7\Core\Config;


use Dotenv\Dotenv;

class Env {
	private $envPath = '';

	private $hostName = '';

	private $defaultName = '.env';

	public function __construct($path) {
		if (empty($path) || !is_dir($path)) {
			throw new \RuntimeException('Invalid env path');
		}
		$this->envPath = rtrim($path, '/');
		$this->hostName = gethostname();
	}

	public function load() {
		//加载当前环境的.env，覆盖默认的.env数据
		$envName = getenv('ENV_NAME') ?: 'default';

		$envFileName = $this->getEnvFileByHostName($envName);
		if (!empty($envFileName) && file_exists($this->envPath . '/' . $envFileName)) {
			putenv('ENV_NAME=' . $envFileName);
			$_ENV['ENV_NAME'] = $envFileName;
			$dotEnv = Dotenv::create($this->envPath, $envFileName);
			$dotEnv->overload();
		}
	}

	private function getEnvFileByHostName($hostname = '') {
		if (empty($hostname)) {
			$hostname = $this->hostName;
		}
		if ($hostname == 'default') {
			return $this->defaultName;
		}

		$fileTree = glob(sprintf('%s/.env*', $this->envPath));
		if (empty($fileTree)) {
			return '';
		}

		$envFile = '';
		foreach ($fileTree as $key => $file) {
			$fileName = pathinfo($file, PATHINFO_BASENAME);
			$temp = explode($this->defaultName . '.', $fileName);
			if (!empty($temp[1]) && strpos($hostname, $temp[1]) !== false) {
				$envFile = $fileName;
			}
		}

		return $envFile;
	}
}