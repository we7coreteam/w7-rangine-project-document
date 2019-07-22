<?php

namespace W7\App\Controller\Admin;


use W7\App;
use W7\Http\Message\Server\Request;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class UserController extends Controller {


    public function addUser(Request $request){
//        C9F8QdEBAUMBFJXB24D
        try{

//            $this->validate($request,[
//                'username' => 'required',
//                'userpass' => 'required',
//                'is_ban' => 'required', // 是否禁用 0 正常 1 禁止
//                'has_privilege' => 'required', // 是否具有特权 0 无 1 有
//            ],[
//                'username.required' => '请输入用户姓名',
//                'userpass.required' => '请输入用户密码',
//                'is_ban.required' => '请设置用户是否禁用',
//                'has_privilege.required' => '请设置用户特权'
//            ]);
//
//            $data = [
//                'username' => $request->input('name'),
//                'userpass' => $request->input('userpass'),
//                'userpass' => $request->input('userpass'),
//                'userpass' => $request->input('userpass'),
//                'userpass' => $request->input('userpass'),
//            ];
//            created_at updated_at

            return $data;
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

    }

    public function verificationCode(){
        $phrase = new PhraseBuilder;
        // 设置验证码位数
        $code = $phrase->build(6);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        // 可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        // 获取验证码的内容
        $phrase = $builder->getPhrase();
        return App::getApp()->getContext()->getResponse()->json($phrase);
        // 把内容存入session
//        Session::flash('code', $phrase);
        // 生成图片
//        header("Cache-Control: no-cache, must-revalidate");
//        header("Content-Type:image/jpeg");
//        $builder->output();
    }


}