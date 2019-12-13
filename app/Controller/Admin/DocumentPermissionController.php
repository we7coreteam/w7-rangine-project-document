<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\Http\Message\Server\Request;

class DocumentPermissionController extends BaseController
{
	public function getAclList(Request $request)
	{
		$list = DocumentPermissionLogic::instance()->getRoleList();
		return $this->data($list);
	}
}