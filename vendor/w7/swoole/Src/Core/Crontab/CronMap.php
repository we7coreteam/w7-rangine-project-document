<?php

namespace W7\Core\Crontab;

class CronMap{
	private $tasks = [];

	public function __construct($config) {
		foreach ($config as $name => $task) {
			$this->add($name, $task);
		}
	}

	public function add($name, $config){
		$this->tasks[$name] = new CrontabTask($name, $config);
	}

	public function rm($name) {
		unset($this->tasks[$name]);
	}

	public function count() {
		return count($this->tasks);
	}

	public function runTask($name) {
		$this->tasks[$name]->run = true;
	}

	public function finishTask($name) {
		$this->tasks[$name]->run = false;
	}

	public function getRunTasks() {
		$time = time();

		$tasks = [];
		foreach ($this->tasks as $task) {
			if ($task->check($time)) {
				$tasks[$task->getName()] = $task->getTask();
			}
		}

		return $tasks;
	}
}