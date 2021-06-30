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

namespace W7\App\Model\Entity\Message;

use Illuminate\Database\Eloquent\SoftDeletes;
use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\User;

class Message extends BaseModel
{
	use SoftDeletes;

	protected $table = 'message';
	protected $fillable = ['from_id', 'to_id', 'text_id', 'type', 'target_type', 'target_id', 'target_url', 'is_read', 'team_id'];
	protected $appends = ['type_text', 'target_type_text', 'target_info', 'time_str'];

	// 系统通知
	const TYPE_REMIND = 'remind';

	//任务
	const REMIND_ARTICLE = 'remind_article'; // 文章通知
	const REMIND_VIDEO = 'remind_video'; // 视频通知

	public static function getTargetTypeLabel($type = '')
	{
		$targetType = [
			// 系统通知
			self::REMIND_ARTICLE => '文章通知',
		];

		$result = [];
		if ($type) {
			foreach ($targetType as $key => $value) {
				if (stripos($key, $type) === 0) {
					$result[$key] = $value;
				}
			}
		} else {
			$result = $targetType;
		}
		return $result;
	}

	// 是否已读
	const IS_READ_Y = 1;
	const IS_READ_N = 2;

	public static function getIsReadLabel()
	{
		return [
			self::IS_READ_Y => '已读',
			self::IS_READ_N => '未读',
		];
	}

	public static function getTypeLabel()
	{
		return [
			self::TYPE_REMIND => '系统通知',
		];
	}

	public function getTypeTextAttribute()
	{
		return self::getTypeLabel()[$this->type] ?? '';
	}

	public function getTargetTypeTextAttribute()
	{
		return self::getTargetTypeLabel()[$this->target_type] ?? '';
	}

	public function getTimeStrAttribute()
	{
		return timeToString($this->created_at->unix());
	}

	public function getTargetInfoAttribute()
	{
		switch ($this->target_type) {
			case self::REMIND_ARTICLE:
				return Article::find($this->target_id);
				break;
			default:
				return '';
		}
	}

	public function text()
	{
		return $this->hasOne(Text::class, 'id', 'text_id');
	}

	public function from()
	{
		return $this->hasOne(User::class, 'id', 'from_id')->select(['id', 'username', 'avatar']);
	}

	public function to()
	{
		return $this->hasOne(User::class, 'id', 'to_id')->select(['id', 'username', 'avatar']);
	}
}
