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

use W7\App\Model\Entity\Article\Article;

/**
 * Class User
 * @package W7\App\Model\Entity
 *
 * @property $isFounder
 * @property $isManager
 * @property $isOperator
 * @property $isReader
 */
class User extends BaseModel
{
	protected $hidden = ['userpass'];
    protected $appends = ['follower_num', 'following_num', 'article_num'];
	const GROUP_ADMIN = 1;

	protected $table = 'user';
	protected $perPage = '10';

	public function document()
	{
		return $this->belongsTo(Document::class);
	}

	public function articles()
    {
        return $this->hasMany(Article::class, 'user_id');
    }

	public function articleCollections()
	{
		return $this->belongsToMany('W7\App\Model\Entity\Article\Article', 'article_collection', 'user_id', 'article_id');
	}

	public function getIsFounderAttribute()
	{
		return $this->group_id == self::GROUP_ADMIN;
	}

    public function getFollowerNumAttribute()
    {
        return $this->followers()->count();
    }

    public function getArticleNumAttribute()
    {
        return $this->articles()->count();
    }

    public function getFollowingNumAttribute()
    {
        return $this->followings()->count();
    }

	public function followers()
	{
		return $this->belongsToMany(User::class, 'user_follower', 'user_id', 'follower_id')->using(UserFollower::class);
	}

	public function followings()
	{
		return $this->belongsToMany(User::class, 'user_follower', 'follower_id', 'user_id')->using(UserFollower::class)->withTimestamps();
	}
}
