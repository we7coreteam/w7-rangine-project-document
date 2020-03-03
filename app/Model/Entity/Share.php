<?php

namespace W7\App\Model\Entity;

class Share extends BaseModel
{
	protected $table = 'user_share';
	protected $primaryKey = 'id';

	public function sharer()
	{
		return $this->belongsTo(User::class, 'id', 'sharer_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'id', 'user_id');
	}
}