<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 15:25
 */

namespace W7\Laravel\CacheModel\Tests\OriginModels;


use Illuminate\Database\Eloquent\Model;

class MemberCount extends Model
{
	public $timestamps = false;
	
	protected $table = 'members_count';
	
	protected $primaryKey = 'uid';
}