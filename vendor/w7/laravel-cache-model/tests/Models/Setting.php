<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 10:37
 */

namespace W7\Laravel\CacheModel\Tests\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	public $timestamps = false;
	
	protected $table = 'settings';
	
	protected $primaryKey = 'key';
	
	protected $keyType = 'string';
	
	protected $connection = 'local';
}