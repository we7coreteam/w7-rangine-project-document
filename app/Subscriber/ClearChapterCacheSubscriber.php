<?php
namespace W7\App\Subscriber;

class ClearChapterCacheSubscriber extends Subscriber {
	public function run($event)
	{
		cache()->delete('chapters_'.$event->chapter['document_id']);
		cache()->delete('chapter_'.$event->chapter['id']);
	}
}
