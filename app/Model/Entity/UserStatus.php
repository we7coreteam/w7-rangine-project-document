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
use W7\App\Model\Entity\Article\ArticleColumn;
use W7\App\Model\Logic\Article\ArticleLogic;

class UserStatus extends BaseModel
{
	protected $table = 'user_status';
	protected $appends = ['time_str', 'status_text', 'status_info'];

	const SHOW = 1; // 显示动态
	const UN_SHOW = 0; // 不显示动态

	const CREATE_DOCUMENT = 1; // 创建文档
	const COLLECT_DOCUMENT = 2; // 收藏/星标文档
	const CREATE_COLUMN = 3; // 创建栏目
	const SUB_COLUMN = 4; // 订阅栏目
	const FOLLOW_USER = 5; // 关注用户
	const CREATE_ARTICLE = 6; // 创建文章

	const STATUS_DOC = [
		self::CREATE_DOCUMENT => '创建了文档',
		self::COLLECT_DOCUMENT => '收藏了文档',
		self::CREATE_COLUMN => '创建了专栏',
		self::SUB_COLUMN => '订阅了专栏',
		self::FOLLOW_USER => '关注了用户',
		self::CREATE_ARTICLE => '创建了文章'
	];

	public function getTimeStrAttribute()
	{
		return timeToString($this->created_at->unix());
	}

	public function getStatusInfoAttribute()
	{
		switch ($this->relation) {
			case 'Document':
				return Document::where('id', $this->relation_id)->with('user')->first();
				break;
			case 'ArticleColumn':
				return ArticleColumn::where('id', $this->relation_id)->with('user')->first();
				break;
			case 'User':
				return User::where('id', $this->relation_id)->first();
				break;
			case 'Article':
				$article = Article::where('id', $this->relation_id)->with('user')->first();
				$article->first_img = ArticleLogic::instance()->getContentFirstImg($article->content, $article->home_thumbnail);
				return $article;
				break;
			default:
				return [];
		}
	}

	public function getStatusTextAttribute()
	{
		return self::STATUS_DOC[$this->type];
	}
}
