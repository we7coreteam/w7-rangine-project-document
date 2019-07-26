<?php
namespace W7\App\Subscriber;

class ClearAuthCacheSubscriber extends Subscriber {
	public function run($event)
	{
		icache()->delete('auth_'.$event->user_id);
		icache()->delete('document_users_'.$event->document_id);
	}
}
