<?php


namespace W7\Core\Route;

class ResourceRoute {
	private $register;
	private $name;
	private $controller;
	private $options = [];
	private $registered;

	public function __construct(ResourceRegister $register, $name, $controller, $options = []) {
		$this->register = $register;
		$this->name = $name;
		$this->controller = $controller;
		$this->options = $options;
	}

	public function only($actions) {
		$this->options['only'] = is_array($actions) ? $actions : func_get_args();

		return $this;
	}

	public function except($actions) {
		$this->options['except'] = is_array($actions) ? $actions : func_get_args();

		return $this;
	}

	public function names($names) {
		$this->options['names'] = $names;

		return $this;
	}

	public function name($action, $name) {
		$this->options['names'][$action] = $name;

		return $this;
	}

	public function parameters($parameters) {
		$this->options['parameters'] = $parameters;

		return $this;
	}

	public function parameter($previous, $new) {
		$this->options['parameters'][$previous] = $new;

		return $this;
	}

	public function middleware($middleware) {
		$this->options['middleware'] = $middleware;

		return $this;
	}

	public function register() {
		$this->registered = true;

		return $this->register->register(
			$this->name, $this->controller, $this->options
		);
	}

	/**
	 * 如果没有手动注册的话执行自动注册
	 */
	public function __destruct() {
		if (! $this->registered) {
			$this->register();
		}
	}
}