<?php
namespace W7\App\Event;

use W7\App\Subscriber\Subscriber;


abstract class Event {

	public $eventType = 'event';
	protected $subscribers = [];
	protected $localSubscribers = []; //内置订阅者

	public function attach($key,$value)
	{
		$this->$key = $value;
		return $this;
	}

	public function addSubscriber($subscriber)
	{
		$key = $this->getKey($subscriber);
		if(!isset($this->subscribers[$key])){
			$this->subscribers[$key] = $subscriber;
		}
	}

	public function addSubscribers($subscribers)
	{
		foreach ($subscribers as $subscriber){
			$this->addSubscriber($subscriber);
		}
	}

	public function dispatch()
	{
		$this->addSubscribers($this->localSubscribers);
		foreach ($this->subscribers as $subscriber){
			$subscriber = new $subscriber();
			if($subscriber instanceof Subscriber){
				$subscriber->run($this);
			}
		}
	}

	private function getKey($key)
	{
		return strtolower(str_replace('\\','_',trim($key,'\\')));
	}
}