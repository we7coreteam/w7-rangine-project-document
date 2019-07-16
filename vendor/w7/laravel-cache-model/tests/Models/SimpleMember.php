<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/28
 * Time: 18:41
 */

namespace W7\Laravel\CacheModel\Tests\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Member
 * @package W7\Laravel\CacheModel\Tests\Models
 *
 * @property int    $uid
 * @property string $invite_code
 * @property string $salt
 * @property string $username
 * @property string $password
 */
class SimpleMember extends Model
{
	public $timestamps = false;
	
	protected $table = 'members';
	
	protected $primaryKey = 'uid';
	
	protected $fillable = [
		'uid',
		'username',
		'password',
		'salt',
		'encrypt',
	];
	
	//	protected $visible = [
	//		'uid', 'mobile', 'password', 'salt',
	//	];
	
	public function memberCount()
	{
		return $this->hasOne(MemberCount::class, 'uid', 'uid');
	}
	
	public function apps()
	{
		return $this->hasMany(App::class, 'uid', 'uid');
	}
}