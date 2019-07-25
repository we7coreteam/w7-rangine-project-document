<?php
namespace W7\App\Subscriber;

abstract class Subscriber {
	abstract public function run($event);
}
