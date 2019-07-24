<?php
namespace W7\App\Subscriber;

class ClearDocumentCacheSubscriber extends SubscriberInterface {
	public function run($event)
	{
		icache()->delete('document_'.$event->id);
	}
}
