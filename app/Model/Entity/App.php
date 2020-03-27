<?php

namespace W7\App\Model\Entity;

class App extends BaseModel
{
	public $timestamps = false;
	protected $connection = 'default';
	protected $table = 'app';
	protected $primaryKey = 'id';
	protected $fillable = [];
}
