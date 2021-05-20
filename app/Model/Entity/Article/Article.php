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

namespace W7\App\Model\Entity\Article;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\User;

class Article extends BaseModel
{
	protected $table = 'article';
	protected $fillable = [
		'column_id', 'tag_ids', 'user_id', 'title', 'content', 'comment_status', 'is_reprint', 'reprint_url', 'home_thumbnail',
		'read_num', 'praise_num', 'status', 'reason',
	];
	protected $appends = ['time_str', 'status_text'];

	const STATUS_CREATE = 0;
	const STATUS_SUCCESS = 1;
	const STATUS_FAIL = 2;

	const COMMENT_STATUS_NO = 0;//不展示评论
	const COMMENT_STATUS_YES = 1;//展示评论

	const IS_REPRINT_NO = 0;//非转载
	const IS_REPRINT_YES = 1;//转载

	public function getContentAttribute($value)
	{
		$option = ['allowed_classes' => false];
		return unserialize($value, $option);
	}

    public function getTimeStrAttribute()
    {
        return timeToString($this->created_at->unix());
    }

	public function setContentAttribute($value)
	{
		$this->attributes['content'] = serialize($value);
	}

	public function getTagIdsAttribute($value)
	{
		return explode(',', $value);
	}

	public function setTagIdsAttribute($value)
	{
		if (is_array($value)) {
			$value = implode(',', $value);
		}
		$this->attributes['tag_ids'] = empty($value) ? '' : $value;
	}

	public static function getStatusLabels()
	{
		return [
			self::STATUS_CREATE => '待审核',
			self::STATUS_SUCCESS => '审核通过',
			self::STATUS_FAIL => '审核失败',
		];
	}

	public function getStatusTextAttribute()
	{
		return self::getStatusLabels()[$this->status] ?? '';
	}

	public function tags()
	{
		return $this->hasMany(ArticleTag::class, 'article_id', 'id')->with('tagConfig');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
