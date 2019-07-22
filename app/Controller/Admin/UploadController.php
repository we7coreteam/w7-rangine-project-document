<?php
namespace W7\App\Controller\Admin;

use W7\App\Model\Service\UploadLogic;
use W7\Http\Message\Server\Request;

class UploadController extends Controller
{
	public function image(Request $request)
	{
		try {
			$image = $request->input('image');
			$uploader = new UploadLogic();
			$url = $uploader->name('ok')->upload($image);
			return $this->success(compact('url'));
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

}
