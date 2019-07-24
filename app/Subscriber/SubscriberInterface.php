<?php
namespace W7\App\Subscriber;

abstract class SubscriberInterface {
	abstract public function run($event);
}
