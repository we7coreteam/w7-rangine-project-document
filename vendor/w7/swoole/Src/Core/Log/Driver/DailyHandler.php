<?php
/**
 * @author donknap
 * @date 18-10-18 下午6:15
 */

namespace W7\Core\Log\Driver;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use W7\Core\Log\HandlerInterface;

class DailyHandler extends RotatingFileHandler implements HandlerInterface {
	const SIMPLE_FORMAT = "[%datetime%] [workid:%workid% coid:%coid%] %channel%.%level_name%: %message% %context% %extra%\n\n";

	static public function getHandler($config) {
		$handler = new static($config['path'], $config['days'], $config['level']);
		$formatter = new LineFormatter(self::SIMPLE_FORMAT);
		$formatter->includeStacktraces(true);
		$handler->setFormatter($formatter);
		return $handler;
	}

	protected function streamWrite($stream, array $record) {
		if (isCo()) {
			go(function() use ($stream, $record) {
				@parent::streamWrite($stream, $record);
			});
		} else {
			@parent::streamWrite($stream, $record);
		}

	}
}