<?php

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Star;
use W7\Core\Helper\Traiter\InstanceTraiter;

class StarLogic extends BaseLogic
{
	use InstanceTraiter;

	public function clearByDocumentId($documentId)
	{
		Star::query()->where('document_id', '=', $documentId)->delete();
	}

	public function clearByUid($userId)
	{
		Star::query()->where('user_id', '=', $userId)->delete();
	}
}