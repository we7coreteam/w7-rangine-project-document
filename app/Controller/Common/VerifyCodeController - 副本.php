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

namespace W7\App\Controller\Common;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\Http\Message\Server\Request;

class VerifyCodeController extends BaseController
{
	const CODE_LENGTH = 4;
	
	public function image(Request $request)
	{
		try {
			$phrase = new PhraseBuilder();
			$code = $phrase->build(self::CODE_LENGTH);

			$builder = new CaptchaBuilder($code, $phrase);
			$builder->setBackgroundColor(255, 255, 255);
			$builder->setMaxAngle(25);
			$builder->setMaxBehindLines(0);
			$builder->setMaxFrontLines(0);
			$builder->build();
			$phrase = $builder->getPhrase();
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
		$request->session->set('img_code', $phrase);

		ob_start();
		$builder->output();
		$image = ob_get_contents();
		ob_end_clean();

		$data = [
			'img' => 'data:image/jpg;base64,'.base64_encode($image)
		];

		ilogger()->debug('verify-code: '. $phrase);
		return $this->data($data);
	}
}
