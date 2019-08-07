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

class VerificationcodeController extends Controller
{
	protected $codeNum = 4;
	protected $width = 100;
	protected $height = 60;

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
			cache()->set($key, $phrase, 60*5);
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
}
