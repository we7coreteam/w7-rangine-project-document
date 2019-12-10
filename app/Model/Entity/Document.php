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

use Illuminate\Support\Str;

class Document extends BaseModel
{
	protected $table = 'document';
	protected $perPage = '10';

	/**
	 * 关联作者
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'creator_id');
	}

	/**
	 * 关联权限表中的操作人员
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function operator() {
		return $this->hasMany(DocumentPermission::class, 'document_id', 'id');
	}

	public function getDescriptionShortAttribute() {
		return Str::limit(html_entity_decode($this->description), 20);
	}
}
