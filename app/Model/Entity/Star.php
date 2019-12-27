<?php

namespace W7\App\Model\Entity;

class Star extends BaseModel
{
	protected $table = 'user_star';
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