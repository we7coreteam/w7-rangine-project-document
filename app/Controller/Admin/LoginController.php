<?php


namespace W7\App\Controller\Admin;


use W7\App\Model\Logic\UserLogic;
use W7\App\Model\Logic\VerificationcodeLogic;
use W7\Http\Message\Server\Request;

class LoginController extends Controller
{

	public function check(Request $request)
	{
		try{
			$this->validate($request, [
				'username' => 'required',
				'userpass' => 'required',
				'code'=> 'required',
				'imgcodeKey'=> 'required',
			], [
				'username.required' => '用户名不能为空',
				'userpass.required' => '密码不能为空',
				'code.required' => '验证码不能为空',
				'imgcodeKey.required' => '验证码的KEY值不能为空',
			]);
			$this->code_logic = new VerificationcodeLogic();
			$code_val = $this->code_logic->getCode($request->input('imgcodeKey')); // c6ia
			if ($request->input(strtolower('code')) != strtolower($code_val)){
				return $this->error('请输入正确的验证码');
			}

			$this->user_longin = new UserLogic();
			$user_val = $this->user_longin->getUser(['username'=>$request->input('username')]);
			if ($user_val){
				$user_pwd = md5(md5($request->input('username').$request->input('userpass')));
				if (isset($user_val['userpass']) && $user_val['userpass'] == $user_pwd){
					return $this->success($user_val);
				}else{
					return $this->error('用户名或者密码有误');
				}
			}else{
				return $this->error('用户不存在');
			}
		}catch(\Exception $e){
			return $this->error($e->getMessage());
		}

	}
}