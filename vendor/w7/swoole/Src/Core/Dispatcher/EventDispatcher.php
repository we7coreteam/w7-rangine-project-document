<?php
/**
 * author: alex
 * date: 18-8-3 上午9:38
 */

namespace W7\Core\Dispatcher;

use W7\App;
use W7\Core\Listener\ListenerInterface;
use W7\Core\Config\Event;

class EventDispatcher extends DispatcherAbstract
{
	private $listener = [];
	private $serverType;
	private $swooleEvent = [];
	private $systemEvent = [];

	/**
	 * EventDispatcher constructor.
	 * @throws \ReflectionException
	 */
	public function __construct()
	{
		$this->serverType = App::$server->type;
		$this->initAllowEvent();
		//用户事件无需手动注册，自动获取即可
		$this->register();
	}

	public function register()
	{
		//根据用户自定义事件列表，添加侦听队列
		$event = \iconfig()->getEvent()['system'];
		if (empty($event)) {
			return true;
		}

		$serverSupport = \iconfig()->getServer();

		$listenerClass = [];
		foreach ($event as $eventName) {
			$listenerClass[$eventName] = [];
			$listenerClass[$eventName]['framework'] = sprintf("\\W7\\Core\\Listener\\%sListener", ucfirst($eventName));
			foreach ($serverSupport as $serverName => $server) {
				$class = sprintf("\\W7\\%s\\Listener\\%sListener", ucfirst($serverName), ucfirst($eventName));
				if (class_exists($class)) {
					$listenerClass[$eventName][$serverName] = $class;
				}
			}
			$listenerClass[$eventName]['user'] = sprintf("\\W7\\App\\Listener\\%sListener", ucfirst($eventName));
		}
		$this->listener = $listenerClass;
		return true;
	}

	/**
	 * @param mixed ...$param
	 * @return bool
	 */
	public function dispatch(...$param)
	{
		$eventName = $param[0];
		$args = $param[1];
		if (!in_array($eventName, $this->systemEvent)) {
			return true;
		}
		$type = ['framework', $this->serverType, 'user'];
		foreach ($type as $item) {
			if (!isset($this->listener[$eventName][$item])) {
				continue;
			}
			$class = $this->listener[$eventName][$item];
			if (!empty($class)) {
				if (class_exists($class)) {
					$object = \iloader()->singleton($class);
					if ($object instanceof ListenerInterface) {
                        call_user_func_array([$object, 'run'], $args);
					}
				}
			}
		}
		return true;
	}

	/**
	 * @return bool
	 * @throws \ReflectionException
	 */
	private function initAllowEvent()
	{
		$eventReflectionClass = new \ReflectionClass(Event::class);
		$event = $eventReflectionClass->getConstants();
		foreach ($event as $eventKey => $eventName) {
			if (strpos($eventKey, '_USER_') !== false) {
				$this->systemEvent[] = $eventName;
			} else {
				$this->swooleEvent[] = $eventName;
			}
		}
		return true;
	}
}
