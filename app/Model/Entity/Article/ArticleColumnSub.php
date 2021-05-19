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

class ArticleColumnSub extends BaseModel
{
	protected $table = 'article_column_sub';
	protected $fillable = [
		'column_id', 'user_id', 'creater_id', 'status', 'sub_time'
	];
	protected $appends = ['status_text'];

	const STATUS_NO = 0;
	const STATUS_CREATER = 1;
	const STATUS_SUB = 2;

	public static function getStatusLabels()
	{
		return [
			self::STATUS_NO => '未关注',
			self::STATUS_CREATER => '创建人',
			self::STATUS_SUB => '已关注',
		];
	}

	public function getStatusTextAttribute()
	{
		return self::getStatusLabels()[$this->status] ?? '';
	}

	public function column()
	{
		return $this->hasOne(ArticleColumn::class, 'id', 'column_id');
	}

	public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
