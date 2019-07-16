<?php
/**
 * @author donknap
 * @date 18-10-18 下午4:26
 */

namespace W7\Core\Log;


interface HandlerInterface {
	static public function getHandler($config);
}