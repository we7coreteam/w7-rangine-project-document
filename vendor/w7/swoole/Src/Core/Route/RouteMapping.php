<?php
/**
 * @author donknap
 * @date 18-8-9 下午3:22
 */

namespace W7\Core\Route;

use W7\Core\Middleware\MiddlewareMapping;

class RouteMapping {

	private $routeConfig;

	/**
	 * @var MiddlewareMapping
	 */
	private $middlewareMapping;

	function __construct() {
		$this->middlewareMapping = iloader()->singleton(MiddlewareMapping::class);
		$this->routeConfig = \iconfig()->getRouteConfig();
		/**
		 * @todo 增加引入扩展机制的路由
		 */
	}

	public function setRouteConfig($routeConfig) {
		$this->routeConfig = $routeConfig;
	}

	public function getRouteConfig() {
		return $this->routeConfig;
	}

	/**
	 * @return array|mixed
	 */
	public function getMapping() {
		if (!empty($this->routeConfig)) {
			foreach ($this->routeConfig as $index => $routeConfig) {
				$this->initRouteByConfig($routeConfig);
			}
		}
		return irouter()->getData();
	}

	private function initRouteByConfig($config, $prefix = '', $middleware = [], $method = '', $name = '', $routeNamespace = '') {
		if (!is_array($config)) {
			return [];
		}

		foreach ($config as $section => $routeItem) {
			//包含prefix时，做为URL的前缀
			if ($section == 'prefix') {
				$prefix .= $routeItem;
				continue;
			}

			//包含method时，做为默认method
			if ($section == 'method') {
				$method = $routeItem;
				continue;
			}

			//仅当下属节点不包含prefix时，才会拼接键名
			if (empty($routeItem['prefix'])) {
				$uri = sprintf('%s/%s', $prefix, ltrim($section, '/'));
			} else {
				$uri = sprintf('%s', $prefix);
			}

			if ($section == 'middleware') {
				$middleware = array_merge([], $middleware, (array) $routeItem);
			}

			if ($section == 'name' && $routeItem) {
				$name .= $routeItem . '.';
			}

			if ($section == 'namespace') {
				$routeNamespace = $routeItem;
				continue;
			}

			if (is_array($routeItem) && !empty($routeItem) && empty($routeItem['handler']) && empty($routeItem['uri'])) {
				$this->initRouteByConfig($routeItem, $uri ?? '', $middleware, $method, $name, $routeNamespace);
			} else {
				if (!is_array($routeItem) || $section == 'middleware' || $section == 'method') {
					continue;
				}
				//如果没有指定Uri,则根据数组结构生成uri
				if (empty($routeItem['uri'])) {
					$routeItem['uri'] = $uri;
				}
				if (empty($routeItem['uri'])) {
					continue;
				}

				//如果没有指定handler，则按数组层级生成命名空间+Controller@当前键名
				if (empty($routeItem['handler'])) {
					$namespace = explode('/', ltrim($uri, '/'));
					$namespace = array_slice($namespace, 0, -1);

					$namespace = array_map('ucfirst', $namespace);
					$routeItem['handler'] = sprintf('%sController@%s', implode("\\", $namespace), $section);
				}

				if (empty($routeItem['method'])) {
					$routeItem['method'] = $method;
				}

				if (empty($routeItem['method'])) {
					$routeItem['method'] = Route::METHOD_BOTH_GP;
				}

				if (is_string($routeItem['method'])) {
					$routeItem['method'] = explode(',', $routeItem['method']);
				}

				if (!isset($routeItem['name'])) {
					$routeItem['name'] = '';
				}
				if (empty($routeItem['name']) && !($routeItem['handler'] instanceof \Closure)) {
					$routeItem['name'] = $name . ltrim(strrchr($routeItem['handler'], '@'), '@');
				}

				//组合中间件
				if (empty($routeItem['middleware'])) {
					$routeItem['middleware'] = [];
				}
				$routeItem['middleware'] = array_merge([], $middleware, (array) $routeItem['middleware']);
				irouter()->group([
					'namespace' => $routeNamespace
				], function () use ($routeItem) {
					irouter()->middleware($routeItem['middleware'])->add(array_map('strtoupper', $routeItem['method']), $routeItem['uri'], $routeItem['handler'], $routeItem['name']);
				});
			}
		}
	}
}