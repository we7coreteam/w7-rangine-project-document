<?php
namespace W7\App\Subscriber;

class ClearAuthCacheSubscriber extends Subscriber {
	public function run($event)
	{
		cache()->delete('auth_'.$event->user_id);
		cache()->delete('document_users_'.$event->document_id);
	}
}
