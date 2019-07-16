<?php
/**
 * @author donknap
 * @date 18-12-11 下午9:14
 */

namespace W7\Core\Helper\Traiter;


trait InstanceTraiter {

	private static $instance;

	static public function instance() {
		if(!isset(self::$instance)){
			self::$instance = new static();
		}
		return self::$instance;
	}

}