<?php
namespace W7\App\Subscriber;

class ClearAuthCacheSubscriber extends SubscriberInterface {
	public function run($event)
	{
		icache()->delete('auth_'.$event->user_id);
	}
}
