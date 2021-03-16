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

class DocumentHome extends BaseModel
{

	protected $table = 'document_home';

	protected $appends=['type_name'];

	//反馈类型
	public $typeName = [1 => '公告', 2=> '首页类型一', 3=> '首页类型二'];

	public function document()
	{
		return $this->belongsTo(Document::class, 'document_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}


	public function getTypeNameAttribute()
	{
		return $this->typeName[$this->attributes['type']];
	}


	public function getDescriptionAttribute(){
		return htmlspecialchars_decode($this->attributes['description'],ENT_QUOTES);
	}


}
