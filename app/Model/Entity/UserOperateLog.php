<?php

namespace W7\App\Model\Entity;

class UserOperateLog extends BaseModel
{
	const CREATE = 1; //创建
	const PREVIEW = 2; //预览
	const EDIT = 3; //编辑
	const DELETE = 4; //删除
	const CHAPTER_MOVE = 5; //移动
	const CHAPTER_COPY = 6; //复制
	const DOCUMENT_TRANSFER = 7; //转让
	const SHARE = 8; //分享
	const COLLECT = 9; //收藏

	protected $table = 'user_operate_log';
	protected $primaryKey = 'id';
	protected $appends = ['time_str'];

	public function getTimeStrAttribute(){
		return timeToString($this->created_at->unix());
	}
	public function setUpdatedAt($value)
	{
		return null;
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id')->select(['id','username','avatar']);
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
