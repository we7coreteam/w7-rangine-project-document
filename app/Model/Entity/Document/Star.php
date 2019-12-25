<?php

namespace W7\App\Model\Entity\Document;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\Document;

class Star extends BaseModel
{
	protected $table = 'document_star';
	protected $primaryKey = 'id';

	public function setUpdatedAt($value)
	{
		return null;
	}

	public function document()
	{
		return $this->hasOne(Document::class, 'id', 'document_id');
	}
}