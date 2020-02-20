<?php

namespace W7\App\Controller\Common;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\MenuSettingLogic;
use W7\Http\Message\Server\Request;

class MenuController extends BaseController
{
	public function setting(Request $request)
	{
		return $this->data(MenuSettingLogic::instance()->getMenuSetting());
	}
}