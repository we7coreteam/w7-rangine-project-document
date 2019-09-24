<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Subscriber;

class ClearChapterCacheSubscriber extends Subscriber
{
	public function run($event)
	{
		icache()->delete('chapters_'.$event->chapter['document_id']);
		icache()->delete('chapter_'.$event->chapter['id']);
	}
}
