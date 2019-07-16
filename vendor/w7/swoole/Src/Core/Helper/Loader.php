<?php
/**
 * 加载助手
 * @author donknap
 * @date 18-7-19 上午10:24
 */

namespace W7\Core\Helper;

class Loader {
	//存储加载过的类
	private $cache = [];
	private $property = [
		'class' => '',
		'single' => false,
		'params' => [],
		'alias' => '',
	];
	private $condition = [];

	/**
	 * 实例化一个不带参数的单例对象
	 */
	public function singleton($name) {
		return $this->withClass($name)->withSingle()->get();
	}

	public function withClass($name) {
		$this->reset();
		if (!class_exists($name)) {
			throw new \Exception($name . '类不存在');
		}
		$this->setProperty('class', $name);
		return $this;
	}

	public function withSingle() {
		return $this->setProperty('single', true);
	}

	public function withParams($name, $value = '') {
		if (is_array($name)) {
			foreach ($name as $var => $val) {
				$this->setProperty('params', [$var => $val]);
			}
			return $this;
		}
		return $this->setProperty('params', [$name => $value]);
	}

	public function withAlias($name) {
		if (is_numeric($name)) {
			throw new \RuntimeException('Alias cannot be a number');
		}
		return $this->setProperty('alias', $name);
	}

	public function get() {
		$className = $this->condition['class'];
		$alias = !empty($this->condition['alias']) ? $this->condition['alias'] : '0';
		$params = (array)$this->condition['params'];

		if (!empty($this->condition['single']) || !empty($this->condition['alias'])) {
			$object = $this->cache[$className][$alias] ?? null;
			if (empty($object)) {
				$object = $this->cache[$className][$alias] = $this->object($className, $params);
			}
		}
		return $object;
	}

	private function setProperty($name, $value) {
		if (is_array($this->property[$name])) {
			$this->condition[$name] = array_merge([], $this->condition[$name], $value);
		} else {
			$this->condition[$name] = $value;
		}
		return $this;
	}

	private function reset() {
		$this->condition = $this->property;
		return $this;
	}

	private function object($name, $params = []) {
		if (!class_exists($name)) {
			throw new \Exception($name . ' not found');
		}

		if (empty($params)) {
			return new $name();
		}

		$reflection = new \ReflectionClass($name);

		if (! $reflection->isInstantiable()) {
			throw new \Exception($name . ' cannot be instantiated');
		}

		$constructor = $reflection->getConstructor();
		if (is_null($constructor)) {
			return new $name();
		}

		$paramsPlace = $constructor->getParameters();
		if (empty($paramsPlace)) {
			return new $name();
		}

		$args = [];
		foreach ($paramsPlace as $place) {
			if (isset($params[$place->getName()])) {
				$args[$place->getName()] = $params[$place->getName()];
			}
		}

		return $reflection->newInstanceArgs($args);
	}
}
