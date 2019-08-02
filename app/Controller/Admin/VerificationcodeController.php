<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
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
	 * @return false|string
	 */
	public function getCodeimg()
	{
		try {
			$phrase = new PhraseBuilder;

			$code = $phrase->build($this->codeNum);

			$builder = new CaptchaBuilder($code, $phrase);

			$builder->setBackgroundColor(220, 210, 230);
			$builder->setMaxAngle(25);
			$builder->setMaxBehindLines(0);
			$builder->setMaxFrontLines(0);
			$builder->build($width = $this->width, $height = $this->height, $font = null);
			$phrase = $builder->getPhrase();

			$key = 'imgCode_'.time().rand();
			$this->code->addCode($key, $phrase, 60*60*5);

			$this->response()->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'image/jpg');
			$this->response()->withoutHeader('Cache-Control')->withAddedHeader('Cache-Control', 'no-cache, must-revalidate');

			ob_start();
			$builder->output();
			$img = ob_get_contents();
			ob_end_clean();

			$img = 'data:image/jpg;base64,'.base64_encode($img);
			$data = [
				'img' => $img,
				'imgcodeKey' => $key
			];
			return $this->success($data);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	/**
	 * 获取验证码
	 * @param Request $request
	 * @return array
	 */
	public function getCode(Request $request)
	{
		try {
			$this->validate($request, [
				'imgcodeKey' => 'required'
			], [
				'imgcodeKey.required' => '验证码的KEY值不能为空',
			]);
			$res = $this->code->getCode($request->input('imgcodeKey'));
			if ($res) {
				return $this->success($res);
			} else {
				return $this->error('验证码已失效');
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
