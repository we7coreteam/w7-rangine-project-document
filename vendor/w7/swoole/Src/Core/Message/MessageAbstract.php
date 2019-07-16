<?php
/**
 * @author donknap
 * @date 18-11-24 下午9:40
 */

namespace W7\Core\Message;



abstract class MessageAbstract {
	static $propertyMapping = [];

	public $messageType;

	public function pack() {
		$classname = static::class;

		if (empty(self::$propertyMapping[$classname])) {
			$reflection = new \ReflectionClass($classname);
			$default = $reflection->getDefaultProperties();

			if ($reflection->getProperties()) {
				foreach ($reflection->getProperties() as $row) {
					if (!$row->isStatic()) {
						self::$propertyMapping[$classname][$row->getName()] = $default[$row->getName()];
					}
				}
			}
		}
		$property = self::$propertyMapping[$classname];

		$data = [
			'class' => static::class
		];
		foreach ($property as $name => $defaultValue) {
			if ($this->$name !== $defaultValue) {
				$data[$name] = $this->$name;
			} else {
				$data[$name] = $defaultValue;
			}
		}
		return serialize($data);
	}
}