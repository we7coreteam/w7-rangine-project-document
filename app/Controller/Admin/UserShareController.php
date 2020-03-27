<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\UserShareLogic;
use W7\Http\Message\Server\Request;

class UserShareController extends BaseController
{
	public function shareUrl(Request $request)
	{
		$params = $this->validate($request, [
			'chapter_id' => 'required|integer',
			'document_id' => 'required|integer'
		]);

		$user = $request->getAttribute('user');

		return $this->data(UserShareLogic::instance()->getShareUrl($user->id, $params['document_id'], $params['chapter_id']));
	}
}