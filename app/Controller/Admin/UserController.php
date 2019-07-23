<?php
namespace W7\App\Controller\Admin;

use W7\Http\Message\Server\Request;
use W7\App\Model\Logic\UserLogic;

class UserController extends Controller
{
	//        C9F8QdEBAUMBFJXB24D

	public function __construct()
	{
		$this->logic = new UserLogic();
	}

	public function addUser(Request $request)
	{
        try{
            $this->validate($request,[
                'username' => 'required',
                'userpass' => 'required',
                'is_ban' => 'required', // 是否禁用 0 正常 1 禁止
                'has_privilege' => 'required', // 是否具有特权 0 无 1 有
            ],[
                'username.required' => '请输入用户姓名',
                'userpass.required' => '请输入用户密码',
                'is_ban.required' => '请设置用户是否禁用',
                'has_privilege.required' => '请设置用户特权'
            ]);

            $data = [
                'username' => $request->input('username'),
                'userpass' => md5(md5($request->input('name').$request->input('userpass'))),
                'is_ban' => $request->input('is_ban'),
                'has_privilege' => $request->input('has_privilege'),
            ];
            if ($request->input('remark')){
				$data['remark'] = $request->input('remark');
			}

            $res = $this->logic->createUser($data);
            if ($res){
				return $this->success($res);
			}
			return $this->error($res);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function updateUser(Request $request)
	{
		try{
			$this->validate($request,[
				'id' => 'required'
			],[
				'id.required' => '用户ID不能为空',
			]);

			$data = [];
			if ($request->input('username')){
				$data['username'] = $request->input('username');
			}
			if ($request->input('is_ban')){
				$data['is_ban'] = $request->input('is_ban');
			}
			if ($request->input('has_privilege')){
				$data['has_privilege'] = $request->input('has_privilege');
			}
			if ($request->input('remark')){
				$data['remark'] = $request->input('remark');
			}

			$res = $this->logic->updateUser(intval($request->input('id')),$data);
			if ($res){
				return $this->success($res);
			}
			return $this->error($res);
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}

	public function softdelUser(Request $request)
	{
		try{
			$this->validate($request,[
				'id' => 'required'
			],[
				'id.required' => '用户ID不能为空',
			]);

			$res = $this->logic->softdelUser(intval($request->input('id')));
			if ($res){
				return $this->success($res);
			}
			return $this->error($res);
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}

	public function delUser(Request $request)
	{
		try{
			$this->validate($request,[
				'id' => 'required'
			],[
				'id.required' => '用户ID不能为空',
			]);

			$res = $this->logic->delUser(intval($request->input('id')));
			if ($res){
				return $this->success($res);
			}
			return $this->error($res);
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}
}