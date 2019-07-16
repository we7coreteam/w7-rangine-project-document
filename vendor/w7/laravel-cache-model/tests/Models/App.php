<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 18:14
 */

namespace W7\Laravel\CacheModel\Tests\Models;


use W7\Laravel\CacheModel\Model;

class App extends Model
{
	public $timestamps = false;
	
	protected $table = 'store_application';
	
	protected $primaryKey = 'id';
}