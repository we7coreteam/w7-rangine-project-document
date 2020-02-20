<?php

namespace W7\App\Model\Logic;

use W7\Core\Helper\Traiter\InstanceTraiter;

class MenuSettingLogic extends BaseLogic
{
	use InstanceTraiter;
	const MENU_SETTING_KEY = 'menu_setting';

	public function all()
	{
		$setting = $this->getMenuSetting();
		$data = $setting['list'] ?? [];
		foreach ($data as $index => &$item) {
			$item['id'] = $index;
		}

		return array_values($data);
	}

	public function getMenuSetting()
	{
		$setting = SettingLogic::instance()->getByKey(self::MENU_SETTING_KEY);
		if (!$setting) {
			return [];
		}

		return $setting->setting;
	}

	public function add(array $config)
	{
		$setting = $this->getMenuSetting();
		$maxId = max(array_keys($setting['list'] ?? [])) + 1;
		$setting['list'][$maxId] = $config;
		SettingLogic::instance()->save(self::MENU_SETTING_KEY, $setting);
	}

	public function getById($id)
	{
		$setting = $this->getMenuSetting();
		return $setting['list'][$id] ?? [];
	}

	public function updateById($id, array $config)
	{
		$setting = $this->getMenuSetting();
		if (empty($setting['list'][$id])) {
			throw new \RuntimeException('该菜单不存在');
		}
		$setting['list'][$id] = $config;
		SettingLogic::instance()->save(self::MENU_SETTING_KEY, $setting);
	}

	public function deleteById($id)
	{
		$setting = $this->getMenuSetting();
		if (!empty($setting['list'][$id])) {
			unset($setting['list'][$id]);
			SettingLogic::instance()->save(self::MENU_SETTING_KEY, $setting);
		}

		return true;
	}

	public function setTheme($theme)
	{
		$setting = $this->getMenuSetting();
		$setting['theme'] = $theme;
		SettingLogic::instance()->save(self::MENU_SETTING_KEY, $setting);
	}

	public function getTheme()
	{
		$setting = $this->getMenuSetting();
		return $setting['theme'] ?? '';
	}
}