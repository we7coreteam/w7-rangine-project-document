<?php
namespace W7\App\Subscriber;

class ClearDocumentCacheSubscriber extends Subscriber {
	public function run($event)
	{
		cache()->delete('document_'.$event->id);
	}
}
