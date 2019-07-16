<?php

namespace W7\Core\Route;


class ResourceRegister {
	protected $router;
	protected $parameters = [];
	protected $resourceDefaults = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

	public function __construct(Route $router) {
		$this->router = $router;
	}

	public function register($name, $controller, $options = []) {
		if (!empty($options['parameters'])) {
			$this->parameters = $options['parameters'];
		}

		if (strpos($name, '/') !== false) {
			$this->prefixedResource($name, $controller, $options);
			return $this;
		}

		$base = $this->getResourceWildcard(last(explode('.', $name)));
		foreach ($this->getResourceActions($options) as $action) {
			$this->{'addResource' . ucfirst($action)}($name, $base, $controller, $options);
		}
	}

	/**
	 * Get the applicable resource methods.
	 *
	 * @param  array  $defaults
	 * @param  array  $options
	 * @return array
	 */
	protected function getResourceActions($options) {
		$methods = $this->resourceDefaults;

		if (isset($options['only'])) {
			$methods = array_intersect($methods, (array) $options['only']);
		}

		if (isset($options['except'])) {
			$methods = array_diff($methods, (array) $options['except']);
		}

		return $methods;
	}

	/**
	 * 替换参数名称
	 * @param  string  $value
	 * @return string
	 */
	protected function getResourceWildcard($value) {
		if (isset($this->parameters[$value])) {
			$value = $this->parameters[$value];
		}

		return str_replace('-', '_', $value);
	}

	/**
	 * 暂不支持多参数，也就是name格式为app.module.test
	 * @param $name
	 * @return string
	 */
	protected function getResourceUri($name) {
		if (!$name) {
			return '';
		}
		return '/' . $name;
	}

	/**
	 * @param $controller
	 * @param $action
	 * @param $options
	 * @return string
	 */
	protected function getResourceHandler($controller, $action, $options) {
		$name = null;
		if (isset($options['names'])) {
			if (is_string($options['names'])) {
				$name = $options['names'];
			} elseif (isset($options['names'][$action])) {
				$name = $options['names'][$action];
			}
		}
		$this->router->name($name);

		if (!empty($options['middleware'])) {
			$this->router->middleware($options['middleware']);
		}

		return $controller . '@' . $action;
	}

	protected function getResourcePrefix($name) {
		$segments = explode('/', $name);
		$prefix = implode('/', array_slice($segments, 0, -1));
		return [end($segments), $prefix];
	}

	/**
	 *如果输入的是/app/module/test 的话，按照分组/app/module为上级分组
	 * @param $name
	 * @param $controller
	 * @param $options
	 * @return bool
	 */
	protected function prefixedResource($name, $controller, $options) {
		[$name, $prefix] = $this->getResourcePrefix($name);

		$callback = function ($route) use ($name, $controller, $options) {
			$route->resource($name, $controller, $options);
		};

		return $this->router->group($prefix, $callback);
	}

	protected function addResourceIndex($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name);
		$handler = $this->getResourceHandler($controller, 'index', $options);

		$this->router->get($uri, $handler);
	}

	protected function addResourceCreate($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name) . '/create';
		$handler = $this->getResourceHandler($controller, 'create', $options);

		$this->router->get($uri, $handler);
	}

	protected function addResourceStore($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name);
		$handler = $this->getResourceHandler($controller, 'store', $options);

		$this->router->post($uri, $handler);
	}

	protected function addResourceShow($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name).'/{'.$base.'}';
		$handler = $this->getResourceHandler($controller, 'show', $options);

		$this->router->get($uri, $handler);
	}

	protected function addResourceEdit($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name).'/{'.$base.'}/edit';
		$handler = $this->getResourceHandler($controller, 'edit', $options);

		$this->router->get($uri, $handler);
	}

	protected function addResourceUpdate($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name).'/{'.$base.'}';
		$handler = $this->getResourceHandler($controller, 'update', $options);

		$this->router->put($uri, $handler);
		$this->router->patch($uri, $handler);
	}

	protected function addResourceDestroy($name, $base, $controller, $options) {
		$uri = $this->getResourceUri($name).'/{'.$base.'}';
		$handler = $this->getResourceHandler($controller, 'destroy', $options);

		$this->router->delete($uri, $handler);
	}
}