<?php
/**
 * @author donknap
 * @date 18-10-18 下午6:26
 */

namespace W7\Core\Log\Driver;


use W7\Core\Log\HandlerInterface;

class ErrorlogHandler extends \Monolog\Handler\ErrorLogHandler  implements HandlerInterface {
	static public function getHandler($config) {
		return new static();
	}
}