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

use W7\App\Model\Entity\Video\Category;
use W7\App\Model\Entity\Video\Comment;
use W7\App\Model\Entity\Video\Praise;

class Video extends BaseModel
{
	protected $table = 'video';
	protected $fillable = ['title', 'cover', 'url', 'description', 'time_length', 'category_ids', 'user_id', 'is_reprint', 'reprint_url', 'status'];
	protected $appends = ['time_str', 'play_num_text'];

	const STATUS_CREATE = 0;
	const STATUS_SUCCESS = 1;
	const STATUS_FAIL = 2;

	const IS_REPRINT_NO = 0;//非转载
	const IS_REPRINT_YES = 1;//转载

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function comment()
	{
		return $this->hasMany(Comment::class, 'video_id', 'id');
	}

	public function praise()
	{
		return $this->hasMany(Praise::class, 'video_id', 'id');
	}

	public function category()
	{
		return $this->hasMany(Category::class, 'video_id', 'id');
	}

	public function getTimeStrAttribute()
	{
		return timeToString($this->created_at->unix());
	}

	public function getPlayNumTextAttribute()
	{
		if ($this->play_num >= 1000) {
			return number_format($this->play_num / 1000, 1) . 'k';
		} else {
			return $this->play_num;
		}
	}

	public function getCategoryIdsAttribute($value)
	{
		return explode(',', $value);
	}

	public function setCategoryIdsAttribute($value)
	{
		if (is_array($value)) {
			$value = implode(',', $value);
		}
		$this->attributes['category_ids'] = empty($value) ? '' : $value;
	}
}
