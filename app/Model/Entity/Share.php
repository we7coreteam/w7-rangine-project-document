<?php

namespace W7\App\Model\Entity;

class Share extends BaseModel
{
	protected $table = 'user_share';
	protected $primaryKey = 'id';

	public function setUpdatedAt($value)
	{
		return null;
	}

	public function sharer()
	{
		return $this->belongsTo(User::class, 'sharer_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}