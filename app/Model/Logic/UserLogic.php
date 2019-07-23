<?php
namespace W7\App\Model\Logic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use W7\App\Model\Entity\User;
class UserLogic extends BaseLogic
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function getUser($data)
	{
		if (isset($data['id'])){
			$user = User::find($data['id']);
		}

		if (isset($data['username'])){
			$user = User::where('username', $data['username'])->first();
		}

		if (isset($data['phone'])){
			$user = User::where('phone', $data['phone'])->first();
		}

		return $user;
	}

	public function createUser($data)
	{
		$users = User::where('username',$data['username'])->count();

		if (!$users){
			return User::create($data);
		}
		return '';
	}

	public function updateUser($id,$data)
	{
		return User::where('id',$id)->update($data);
	}

	public function softdelUser($id)
	{
		Schema::table('users',function ($table){
			$table->softDeletes();
		});
		return User::find($id)->delete();
	}

	public function delUser($ids)
	{
		return User::destroy($ids);
	}

	public function searchUser($data)
	{
		if (isset($data['username'])){
			return User::where('username','like','%'.$data['username'].'%')->get();
		}
	}

}
