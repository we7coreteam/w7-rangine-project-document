<?php


namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\HomepageSettingLogic;
use W7\Http\Message\Server\Request;
//首页文档设置
class DocumentHomeController extends BaseController
{
	protected  $_user;

	private function check(Request $request)
	{
		 $this->_user= $request->getAttribute('user');
		if (!$this->_user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}






}
