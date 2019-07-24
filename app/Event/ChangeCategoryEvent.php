<?php
namespace W7\App\Event;

use W7\Core\Helper\Traiter\InstanceTraiter;

class ChangeCategoryEvent extends Event
{
	use InstanceTraiter;

	public $eventType = 'change_category';

}