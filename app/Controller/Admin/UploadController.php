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

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\SettingLogic;
use W7\App\Model\Service\CdnLogic;
use W7\Http\Message\Server\Request;

class UploadController extends BaseController
{
	public function image(Request $request)
	{
		$this->validate($request, [
			'file' => 'required|file|mimes:bmp,png,jpeg,jpg|max:2048'
		]);

		$file = $request->file('file');

		$fileName = sprintf('/%s.%s', irandom(32), pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
		try {
			$url = CdnLogic::instance()->channel(SettingLogic::KEY_COS)->uploadFile($fileName, $file->getRealPath());
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data(['url' => $url]);
	}
}
