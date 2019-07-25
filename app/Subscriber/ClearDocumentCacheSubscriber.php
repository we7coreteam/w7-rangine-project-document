<?php
namespace W7\App\Subscriber;

class ClearDocumentCacheSubscriber extends Subscriber {
	public function run($event)
	{
		icache()->delete('document_'.$event->id);
	}
}
