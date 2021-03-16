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

class DocumentFeedback extends BaseModel
{
	//反馈类型
	const NOT_FOUND_TYPE = 0;
	const NO_UPDATE_TYPE = 1;
	const NOT_KNOWN_TYPE = 2;
	const HAS_ERROR_TYPE = 3;
	const NO_COMPLETE_TYPE = 4;
	const HAS_DEFECT_TYPE = 5;

	private $permissionName = [
		self::NOT_FOUND_TYPE => '内容找不到',
		self::NO_UPDATE_TYPE => '内容没更新',
		self::NOT_KNOWN_TYPE => '描述不清楚',
		self::HAS_ERROR_TYPE => '链接有错误',
		self::NO_COMPLETE_TYPE => '步骤不完整',
		self::HAS_DEFECT_TYPE => '代码/图片缺失'
	];

	protected $table = 'document_feedback';

	protected $appends=['type_name','status_text'];

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
		$type = explode(',',$this->attributes['type']);
		$typeArr = array_intersect_key($this->permissionName,array_flip($type));
		return !empty($typeArr) ? implode(',',$typeArr) : '';
	}


	public function getStatusTextAttribute(){
		$statusText = [0=>'未读',1=>'已读'];
		return $statusText[$this->attributes['status']];
	}


    public function getImagesAttribute(){
		return $this->attributes['images'] ? json_decode($this->attributes['images'],true): [];
	}

	public function getContentAttribute(){
		return htmlspecialchars_decode($this->attributes['content'],ENT_QUOTES);
	}


}
