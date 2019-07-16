<?php
/**
 * @author donknap
 * @date 18-8-9 下午4:06
 */

namespace W7\Core\Middleware;

use W7\App;

class MiddlewareMapping {
	function __construct() {

	}

	/**
	 * 获取当前启动组件服务中定义的固定last中间件
	 */
	public function getLastMiddle() {
		if (empty(App::$server->type)) {
			return [];
		}
		$class = sprintf("\\W7\\%s\\Middleware\\LastMiddleware", ucfirst(App::$server->type));
		if (class_exists($class)) {
			return [$class];
		} else {
			return [];
		}
	}
}