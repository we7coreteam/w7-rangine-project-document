<?php

namespace W7\App\Model\Entity;

class UserOperateLog extends BaseModel
{
	const CREATE = 1;
	const PREVIEW = 2;
	const EDIT = 3;
	const DELETE = 4;
	const CHAPTER_MOVE = 5;
	const CHAPTER_COPY = 6;
	const DOCUMENT_TRANSFER = 7;
	const SHARE = 8;

	protected $table = 'user_operate_log';
	protected $primaryKey = 'id';

	public function setUpdatedAt($value)
	{
		return null;
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function targetUser()
	{
		return $this->belongsTo(User::class, 'target_user_id', 'id');
	}

	public function document()
	{
		return $this->belongsTo(Document::class, 'document_id', 'id');
	}

	public function getOperateDescAttribute()
	{
		switch ($this->operate) {
			case self::CREATE:
				return '创建';
			case self::PREVIEW:
				return '预览';
			case self::EDIT:
				return '编辑';
			case self::DELETE:
				return '删除';
		}
	}
}