<?php
/**
 * 验证码类
 */

namespace W7\App\Controller\Admin;

use W7\App;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

use W7\App\Model\Logic\VerificationcodeLogic;
use W7\Http\Message\Server\Request;

class VerificationcodeController extends Controller
{
    public function __construct()
    {
        $this->code = new VerificationcodeLogic();
    }

    public function getCodeimg(Request $request){
//        try{
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
            // 把内容存入 缓存
            // Session::flash('code', $phrase);
            $this->code->addCode($request->document_user_id,$phrase,60);
            // 生成图片
//            header("Cache-Control: no-cache, must-revalidate");
            header("Content-Type:image/jpeg");
            $builder->output();
//        }catch (\Exception $e){
//            return $this->error($e->getMessage());
//        }

    }

    public function getCode(Request $request){
        return $this->code->getCode($request->document_user_id);
    }


}