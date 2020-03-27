<?php

namespace W7\App\Model\Entity;

use W7\App\Model\Entity\Document\Chapter;

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
		return $this->belongsTo(Document::class, 'document_id', 'id');
	}

	public function chapter()
	{
		return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
	}
}