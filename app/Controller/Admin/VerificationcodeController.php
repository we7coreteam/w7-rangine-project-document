<?php
/**
 * 验证码类
 */
namespace W7\App\Controller\Admin;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

use W7\App\Model\Logic\VerificationcodeLogic;
use W7\Http\Message\Server\Request;


class VerificationcodeController extends Controller
{
	protected $codeNum = 4;
	protected $width = 100;
	protected $height = 60;

    public function __construct()
    {
        $this->code = new VerificationcodeLogic();
    }

	/**
	 * 获取验证码图片
	 * @param Request $request
	 * @return false|string
	 */
    public function getCodeimg(Request $request){
        try{
            $phrase = new PhraseBuilder;

            $code = $phrase->build($this->codeNum);

            $builder = new CaptchaBuilder($code, $phrase);

            $builder->setBackgroundColor(220, 210, 230);
            $builder->setMaxAngle(25);
            $builder->setMaxBehindLines(0);
            $builder->setMaxFrontLines(0);
            $builder->build($width = $this->width, $height = $this->height, $font = null);
            $phrase = $builder->getPhrase();
            $this->code->addCode($request->document_user_id,$phrase,60);

			$this->response()->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'image/jpg');
			$this->response()->withoutHeader('Cache-Control')->withAddedHeader('Cache-Control', 'no-cache, must-revalidate');

			ob_start();
		 	$builder->output();
		 	$img = ob_get_contents();
			ob_end_clean();

			$img = 'data:image/jpg;base64,'.base64_encode($img);
			return $this->success($img);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

	/**
	 * 获取验证码
	 * @param Request $request
	 * @return array
	 */
    public function getCode(Request $request){
    	try{
			return $this->success($this->code->getCode($request->document_user_id));
		}catch(\Exception $e){
			return $this->error($e->getMessage());
		}
    }


}