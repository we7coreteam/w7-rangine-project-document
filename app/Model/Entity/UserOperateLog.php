<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

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
	protected $appends = ['time_str', 'operate_desc'];

	public function getTimeStrAttribute()
	{
		return timeToString($this->created_at->unix());
	}

	public static function getOperateLabel()
	{
		return [
			self::CREATE => '创建',
			self::PREVIEW => '预览',
			self::EDIT => '编辑',
			self::DELETE => '删除',
			self::CHAPTER_MOVE => '移动',
			self::CHAPTER_COPY => '复制',
			self::DOCUMENT_TRANSFER => '转让',
			self::SHARE => '分享',
			self::COLLECT => '收藏'
		];
	}

	public function getOperateDescAttribute()
	{
		return self::getOperateLabel()[$this->operate] ?? '';
	}

	public function setUpdatedAt($value)
	{
		return null;
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id')->select(['id', 'username', 'avatar']);
	}

	public function targetUser()
	{
		return $this->belongsTo(User::class, 'target_user_id', 'id');
	}

	public function document()
	{
		return $this->belongsTo(Document::class, 'document_id', 'id');
	}
}
