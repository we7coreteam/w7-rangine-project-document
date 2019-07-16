<?php

namespace W7\Core\Route;

class RouteCollector extends \FastRoute\RouteCollector {
	public function getCurrentGroupPrefix() {
		return $this->currentGroupPrefix;
	}
}