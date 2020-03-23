<?php

namespace W7\App\Controller\Common;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\MenuSettingLogic;
use W7\Http\Message\Server\Request;

class MenuController extends BaseController
{
	public function setting(Request $request)
	{
		$setting = MenuSettingLogic::instance()->getMenuSetting();
		$list = $setting['list'] ?? [];
		$sorts = array_column($list, 'sort');
		array_multisort($sorts, SORT_ASC, $list);
		foreach ($list as $index => &$item) {
			$item['id'] = $index;
		}
		$setting['list'] = array_values($list);
		return $this->data($setting);
	}
}