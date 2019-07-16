<?php
/**
 * @author donknap
 * @date 18-12-17 下午8:18
 */

namespace W7\Core\Route;

use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased;

class Route {
	const METHOD_POST = 'POST';
	const METHOD_GET = 'GET';
	const METHOD_BOTH_GP = 'POST,GET';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';
	const METHOD_HEAD = 'HEAD';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_ALL = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

	private $router;

	/**
	 * 当前路由中间件
	 * @var array
	 */
	private $currentMiddleware = [];
	private $groupMiddleware = [];
	private $groupName = [];
	private $groupNamespace;
	private $groupModule;
	private $defaultNamespace = 'W7\App';
	private $defaultModule = 'system';

	private $name = '';

	public function __construct() {
		$this->router = new RouteCollector(new Std(), new GroupCountBased());
	}

	private function parseGroupOption($option) {
		if (!is_array($option)) {
			$prefix = $option;
			$option = [
				'prefix' => $prefix
			];
		}
		return [
			'prefix' => $option['prefix'] ?? '',
			'namespace' => empty($option['namespace']) ? $this->defaultNamespace : $option['namespace'],
			'module' => $option['module'] ?? $this->defaultModule
		];
	}

	/**
	 * middleware按照分组隔开，子分组的middleware始终含有父的middleware
	 * @param $prefix
	 * @param callable $callback
	 * @return bool
	 */
	public function group($option, callable $callback) {
		$option = $this->parseGroupOption($option);

		$parentPrefix = $this->router->getCurrentGroupPrefix();
		$this->router->addGroup($option['prefix'], function (RouteCollector $route) use ($callback, $option, $parentPrefix) {
			$prefix = $this->router->getCurrentGroupPrefix();

			$groupNamespace = $this->groupNamespace[$parentPrefix] ?? $option['namespace'];
			$this->groupNamespace[$prefix] = $groupNamespace;

			$groupMiddleware = array_merge($this->groupMiddleware[$parentPrefix] ?? [], $this->checkMiddleware($this->currentMiddleware));
			$this->groupMiddleware[$prefix] = $groupMiddleware;
			$this->currentMiddleware = [];

			$groupName = $this->groupName[$parentPrefix] ?? '';
			if ($this->name) {
				$groupName .= $this->name . '.';
			}
			$this->groupName[$prefix] = $groupName;
			$this->name = '';

			$groupModule = $this->groupModule[$parentPrefix] ?? $option['module'];
			$this->groupModule[$prefix] = $groupModule;

			$callback($this);

			unset($this->groupMiddleware[$prefix]);
			unset($this->groupName[$prefix]);
			unset($this->groupNamespace[$prefix]);
			unset($this->groupModule[$prefix]);
		});
		return true;
	}

	/**
	 * 注册一个允许所有协议的路由
	 * @param $route
	 * @param $handler
	 */
	public function any($uri, $handler) {
		return $this->add(self::METHOD_ALL, $uri, $handler);
	}

	public function all($uri, $handler) {
		$this->any($uri, $handler);
	}

	/**
	 * 注册一个Post 路由
	 */
	public function post($uri, $handler) {
		$result = $this->add(self::METHOD_POST, $uri, $handler);
		return $result;
	}

	public function get($uri, $handler) {
		$result = $this->add(self::METHOD_GET, $uri, $handler);
		return $result;
	}

	public function put($uri, $handler) {
		$result = $this->add(self::METHOD_PUT, $uri, $handler);
		return $result;
	}

	public function delete($uri, $handler) {
		$result = $this->add(self::METHOD_DELETE, $uri, $handler);
		return $result;
	}

	public function patch($uri, $handler) {
		$result = $this->add(self::METHOD_PATCH, $uri, $handler);
		return $result;
	}

	public function head($uri, $handler) {
		$result = $this->add(self::METHOD_HEAD, $uri, $handler);
		return $result;
	}

	public function options($uri, $handler) {
		$result = $this->add(self::METHOD_OPTIONS, $uri, $handler);
		return $result;
	}

	/**
	 * 注册一个支持多种协议的路由
	 * @param $methods
	 * @param $uri
	 * @param $handler
	 */
	public function add($methods, $uri, $handler, $name = '') {
		$handler = $this->checkHandler($handler);

		if (empty($methods)) {
			$methods = self::METHOD_BOTH_GP;
		}
		if (!is_array($methods)) {
			$methods = [$methods];
		}
		//清除掉Method两边的空格
		foreach ($methods as &$value) {
			$value = strtoupper(trim($value));
		}
		unset($value);

		$routeHandler = [
			'handler' => $handler,
			'module' => $this->getModule(),
			'controller_namespace' => $this->getNamespace() . '\Controller\\',
			'middleware_namespace' => $this->getNamespace() . '\Middleware\\',
			'middleware' => [
				'before' => [],
				'after' => []
			],
			'uri' => $this->router->getCurrentGroupPrefix() . $uri
		];

		if (!$name) {
			$name = $this->name;
		}
		if (!$name && !($handler instanceof \Closure)) {
			$name = ($this->groupName[$this->router->getCurrentGroupPrefix()] ?? '') . $handler[1];
		}
		$routeHandler['name'] = $name;

		//处理namespace
		if (!($routeHandler['handler'] instanceof \Closure)) {
			$routeHandler['handler'][0] = $this->prependGroupNamespace($routeHandler['controller_namespace'], $routeHandler['handler'][0]);
		}

		//先获取上级的middleware
		//添加完本次路由后，要清空掉当前Middleware值，以便下次使用
		$groupMiddleware = $this->groupMiddleware[$this->router->getCurrentGroupPrefix()] ?? [];
		$routeHandler['middleware']['before'] = array_merge($groupMiddleware, $routeHandler['middleware']['before'], $this->checkMiddleware($this->currentMiddleware));

		$this->currentMiddleware = [];
		$this->name = '';

		$this->router->addRoute($methods, $uri, $routeHandler);
		return true;
	}

	/**
	 * 注册一个直接跳转路由
	 * @param $uri
	 * @param $destination
	 * @param int $status
	 */
	public function redirect($uri, $destination, $status = 301) {
		throw new \InvalidArgumentException('还未实现');
	}

	/**
	 * 注册一个直接显示的静态页
	 * @param $uri
	 * @param $view
	 * @param array $data
	 */
	public function view($uri, $view, $data = []) {
		throw new \InvalidArgumentException('还未实现');
	}

	public function resource($name, $controller, $options = []) {
		return new ResourceRoute(new ResourceRegister($this), $name, $controller, $options);
	}

	public function apiResource($name, $controller, $options = []) {
		return new ResourceRoute(new ResourceRegister($this), $name, $controller, $options);
	}

	protected function prependGroupNamespace($namespace, $class) {
		return (strpos($class, $namespace) === false && strpos($class, '\\') !== 0)
			? $namespace . $class : $class;
	}

	public function middleware($name) {
		if (!is_array($name)) {
			$name = func_get_args();
			$name = [$name];
		}
		foreach ($name as $i => $row) {
			if (!is_array($row)) {
				$row = [$row];
			}
			$this->currentMiddleware[] = $row;
		}

		return $this;
	}

	/**
	 * 指定该路由的名字，用于验权之类的操作
	 * @param $name
	 */
	public function name($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * 获取路由列表
	 * @return array
	 */
	public function getData() {
		return $this->router->getData();
	}

	private function checkHandler($handler) {
		if ($handler instanceof \Closure) {
			return $handler;
		}
		if (is_string($handler)) {
			$handler = explode('@', $handler);
		}
		list($className, $action) = $handler;
		if (empty($action)) {
			$action = 'index';
		}

		return [
			$className,
			$action,
		];
	}

	private function checkMiddleware($middleware) {
		if (!is_array($middleware)) {
			$middleware = [$middleware];
		}
		foreach ($middleware as $index => $class) {
			if (!is_array($class)) {
				$class = [$class];
			}

			$namespace = $this->getNamespace() . '\Middleware\\';
			$class[0] = $this->prependGroupNamespace($namespace, $class[0]);
			$middleware[$index] = $class;
		}
		return $middleware;
	}

	private function getNamespace() {
		return $this->groupNamespace[$this->router->getCurrentGroupPrefix()] ?? $this->defaultNamespace;
	}

	private function getModule() {
		return $this->groupModule[$this->router->getCurrentGroupPrefix()] ?? $this->defaultModule;
	}
}