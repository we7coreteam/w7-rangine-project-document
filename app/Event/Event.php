<?php
namespace W7\App\Event;

use W7\App\Subscriber\SubscriberInterface;


class Event {

	public $eventType = 'event';
	protected $subscribers = [];

	public function attach($key,$value)
	{
		$this->$key = $value;
		return $this;
	}

	public function addSubscriber($subscriber,$key=null)
	{
		if($key === null){
			$key = str_replace('\\','_',$subscriber);
		}
		if(!isset($this->subscribers[$key])){
			$this->subscribers[$key] = $subscriber;
		}
	}

	public function dispatch()
	{
		foreach ($this->subscribers as $k=>$subscriber){
			$subscriber = new $subscriber();
			if($subscriber instanceof SubscriberInterface){
				$subscriber->run($this);
			}
		}
	}
}