<?php
namespace W7\App\Controller\Admin;

use W7\App\Model\Service\CdnLogic;
use W7\Http\Message\Server\Request;

class UploadController extends Controller
{
	public function image(Request $request)
	{
		try {













			$file = $request->file('icon');
			if ($file) {
				$file = $file->toArray();
			} else {
				return $this->error('icon必传');
			}

			$allowed_mime = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg'];
			if (0 !== $file['error']) {
				return $this->error('['.$file['error'].']上传失败！网络错误或文件过大');
			}
			if (isset($file['type']) && !in_array($file['type'], $allowed_mime, true)) {
				return $this->error('only jpg,jpeg,png,gif allowed');
			}
			if ($file['size'] > 2 * 1024 * 1204) {
				return $this->error('图片尺寸不得超过2M');
			}

			$baseName = md5(time().str_random(10).uniqid());
			$fileName = $baseName.'.'.explode('/', $file['type'])[1];
			$cdn = new CdnLogic();
			$url = $cdn->uploadFile('dc/'.$fileName, $file['tmp_file']);

			return $this->success(compact('url'));
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
