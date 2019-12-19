<?php

namespace W7\App\Model\Entity\Document;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\User;

class ChapterOperateLog extends BaseModel
{
	const CREATE = 1;
	const EDIT = 2;
	const DELETE = 3;

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
}