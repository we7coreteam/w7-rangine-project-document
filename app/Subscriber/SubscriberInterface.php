<?php
namespace W7\App\Subscriber;

use W7\App\Event\Event;

abstract class SubscriberInterface {
	abstract public function run($event);
}
