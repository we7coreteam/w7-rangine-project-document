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

		$fileName = irandom(32) . '.' . pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);

		$url = CdnLogic::instance()->channel('document')->uploadFile($fileName, $file->getTmpFile());
		echo $url;exit;
	}

	public function index(Request $request)
	{
		$this->validate($request, [
			'action' => 'required',
		], [
			'action.required' => 'action必填',
		]);
		$action = $request->input('action');
		if ($action == 'config') {
			$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', file_get_contents(BASE_PATH.'/config/ueditor.json')), true);
			return $CONFIG;
		}
		throw new ErrorHttpException('action值有误');
	}
}
