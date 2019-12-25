<?php

namespace W7\App\Model\Entity\Document;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\User;

class ChapterOperateLog extends BaseModel
{
	const CREATE = 1;
	const PREVIEW = 2;
	const EDIT = 3;
	const DELETE = 4;

	protected $table = 'document_chapter_operate_log';
	protected $primaryKey = 'id';

	public function setUpdatedAt($value)
	{
		return null;
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	public function document()
	{
		return $this->hasOne(Document::class, 'id', 'document_id');
	}
}