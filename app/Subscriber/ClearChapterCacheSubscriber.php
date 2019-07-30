<?php
namespace W7\App\Subscriber;

class ClearChapterCacheSubscriber extends Subscriber {
	public function run($event)
	{
		icache()->delete('chapters_'.$event->chapter['document_id']);
		icache()->delete('chapter_'.$event->chapter['id']);
	}
}
