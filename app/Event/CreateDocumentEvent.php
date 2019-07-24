<?php
namespace W7\App\Event;

use W7\Core\Helper\Traiter\InstanceTraiter;

class CreateDocumentEvent extends Event
{
	use InstanceTraiter;
	public $eventType = 'create_document';

}