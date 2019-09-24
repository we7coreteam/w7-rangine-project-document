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

use W7\App\Model\Service\CdnLogic;
use W7\Http\Message\Server\Request;

class UploadController extends Controller
{
	public function image(Request $request)
	{
		try {
			$file = $request->file('file');
			if ($file) {
				$file = $file->toArray();
			} else {
				return $this->error('file必传');
			}

			$allowed_mime = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg'];
			if (0 !== $file['error']) {
				return ['success' => 0,'message' => '['.$file['error'].']上传失败！网络错误或文件过大'];
			}
			if (isset($file['type']) && !in_array($file['type'], $allowed_mime, true)) {
				return ['success' => 0,'message' => 'only jpg,jpeg,png,gif allowed'];
			}
			if ($file['size'] > 2 * 1024 * 1204) {
				return ['success' => 0,'message' => '图片尺寸不得超过2M'];
			}

			$baseName = md5(time().irandom(1000,9999).uniqid());
			$fileName = $baseName.'.'.explode('/', $file['type'])[1];

			$cdn = new CdnLogic();
			$url = $cdn->uploadFile('dc/'.$fileName, $file['tmp_name']);

			return ['state' => 'SUCCESS' ,'success' => 1,'message' => '上传成功','url'=>$url];
		} catch (\Exception $e) {
			return ['success' => 0,'message' => $e->getMessage()];
		}
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
		return $this->error('action值有误');
	}
}
