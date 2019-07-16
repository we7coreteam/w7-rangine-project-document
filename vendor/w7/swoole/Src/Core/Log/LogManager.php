<?php
/**
 * @author donknap
 * @date 18-10-18 下午3:40
 */

namespace W7\Core\Log;

use Monolog\Handler\BufferHandler;
use Monolog\Logger as MonoLogger;
use W7\App;
use W7\Core\Log\Processor\SwooleProcessor;

class LogManager {
	private $channel = [];
	private $config;
	private $commonProcessor;
	private $commonSetting;

	public function __construct() {
		$this->config = $this->getConfig();
		$this->commonSetting = iconfig()->getUserAppConfig('setting');

		if ((ENV & CLEAR_LOG) === CLEAR_LOG) {
			$this->cleanLogFile();
		}
		if (empty($this->config['channel'])) {
			throw new \RuntimeException('Invalid log config');
		}
		//初始化全局附加的Handler, Processor, Formatter
		$this->commonProcessor = $this->initCommonProcessor();

		$this->initChannel();
	}

	public function getDefaultChannel() {
		if (empty($this->config['default'])) {
			throw new \RuntimeException('It is not set default logger');
		}
		return $this->getChannel($this->config['default']);
	}

	public function getChannel($name) {
		if (isset($this->channel[$name]) && $this->channel[$name]['logger'] instanceof MonoLogger) {
			return $this->channel[$name]['logger'];
		} else {
			//不存在指定的日志通道时，返回默认
			return $this->getDefaultChannel();
		}
	}

	public function getLoggers($channel = null) {
		if ($channel) {
			return [$this->channel[$channel]['logger']];
		}

		return array_column($this->channel, 'logger');
	}

	/**
	 * 初始化通道，
	 * @param $channelConfig
	 * @return bool
	 */
	private function initChannel() {
		$stack = [];
		$channelConfig = $this->config['channel'];
		//先初始化单个通道，记录下相关的Handler，再初始化复合通道
		foreach ($channelConfig as $name => $channel) {
			if (empty($channel['driver'])) {
				continue;
			}
			if ($channel['driver'] == 'stack') {
				$stack[$name] = $channel;
			} else {
				$handlerClass = sprintf("\\W7\\Core\\Log\\Driver\\%sHandler", ucfirst($channel['driver']));
				$bufferLimit = $channel['buffer_limit'] ?? 1;
				$handler = new BufferHandler($handlerClass::getHandler($channel), $bufferLimit, $channel['level'], true, true);

				if (!is_null($handler)) {
					$logger = $this->getLogger($name);
					$logger->pushHandler($handler);
				}

				$this->channel[$name]['handler'] = $handler;
				$this->channel[$name]['logger'] = $logger;
			}
		}

		if (!empty($stack)) {
			foreach ($stack as $name => $setting) {
				$logger = $this->getLogger($name);

				if (is_array($setting['channel'])) {
					foreach ($setting['channel'] as $channel) {
						if (!empty($this->channel[$channel]) && !is_null($this->channel[$channel]['handler'])) {
							$logger->pushHandler($this->channel[$channel]['handler']);
						}
					}
				} else {
					if (!is_null($this->channel[$channel]['handler'])) {
						$logger->pushHandler($this->channel[$setting['channel']]['handler']);
					}
				}

				$this->channel[$name]['logger'] = $logger;
			}
		}

		return true;
	}

	private function initCommonProcessor() {
		$swooleProcessor = iloader()->singleton(SwooleProcessor::class);
		//不记录产生日志的文件和行号
		//异常中会带，普通日志函数又是一样的
		//$introProcessor = iloader()->singleton(IntrospectionProcessor::class);
		return [
			$swooleProcessor,
			//$introProcessor
		];
	}

	private function getConfig() {
		$config = iconfig()->getUserConfig('log');
		if (!empty($this->config['channel'])) {
			foreach ($this->config['channel'] as $name => &$setting) {
				if (!empty($setting['level'])) {
					$setting['level'] = MonoLogger::toMonologLevel($setting['level']);
				}
			}
		}
		return $config;
	}

	private function getLogger($name) {
		$logger = new Logger($name, [], []);
		$logger->bufferLimit = $this->config['channel'][$name]['buffer_limit'] ?? 1;

		if (!empty($this->commonProcessor)) {
			foreach ($this->commonProcessor as $processor) {
				$logger->pushProcessor($processor);
			}
		}
		return $logger;
	}

	private function cleanLogFile() {
		$logPath = RUNTIME_PATH . DS. 'logs/*';
		$tree = glob($logPath);
		if (!empty($tree)) {
			foreach ($tree as $file) {
				if (strstr($file, '.log') !== false) {
					unlink($file);
				}
			}
		}
		return true;
	}
}