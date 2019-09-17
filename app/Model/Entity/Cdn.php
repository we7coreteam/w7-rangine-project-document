<?php


namespace W7\App\Model\Entity;


class Cdn extends BaseModel
{
	public $timestamps = false;
	protected $table = 'cdn';
	protected $primaryKey = 'key';
}
