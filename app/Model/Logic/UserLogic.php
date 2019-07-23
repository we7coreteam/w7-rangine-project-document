<?php
namespace W7\App\Model\Logic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use W7\App\Model\Entity\User;
class UserLogic extends BaseLogic
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

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

	public function delUser($id)
	{
		return User::destroy($id);
	}

}
