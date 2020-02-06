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

namespace W7\App\Model\Logic;

use W7\Core\Helper\Traiter\InstanceTraiter;

class ThirdPartyLoginLogic extends BaseLogic
{
	use InstanceTraiter;
	const THIRD_PARTY_LOGIN_SETTING_KEY = 'third_party_login';

	public function __construct()
	{
		$setting = $this->getThirdPartyLoginSetting();
		if (empty($setting['channel'])) {
			$setting['channel'] = [
				[
					'is_default' => true,
					'setting' => [
						'app_union_key' => 'qq',
						'name' => 'QQ',
						'logo' => '//cdn.w7.cc/web/resource/images/wechat/qqlogin.png'
					]
				],
				[
					'is_default' => true,
					'setting' => [
						'app_union_key' => 'wechat',
						'name' => '微信',
						'logo' => '//cdn.w7.cc/web/resource/images/wechat/wxlogin.png'
					]
				]
			];
			SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
		}
	}

    public function getThirdPartyLoginSetting()
    {
		$setting = SettingLogic::instance()->getByKey(self::THIRD_PARTY_LOGIN_SETTING_KEY);
		if (!$setting) {
			return [];
		}

		return $setting->setting;
	}


    public function getThirdPartyLoginChannelByName($name)
    {
		$setting = $this->getThirdPartyLoginSetting();
		$nameArr = array_column(array_column($setting['channel'], 'setting'), 'name');
		$index = array_search($name, $nameArr);
		if ($index === false) {
			return false;
		}

		//判断是不是默认支持的
		$setting['channel'][$index]['is_default'] = $setting['channel'][$index]['is_default'] ?? false;
		return $setting['channel'][$index];
	}

    public function getThirdPartyLoginChannelById($id)
    {
		$setting = $this->getThirdPartyLoginSetting();
		$setting = $setting['channel'][$id - 1] ?? [];
		//判断是不是默认支持的
		$setting['is_default'] = $setting['is_default'] ?? false;
		return $setting;
	}

	public function deleteThirdPartyLoginChannelById($id) {
		$setting = $this->getThirdPartyLoginSetting();
		if (!empty($setting['channel'][$id - 1])) {
			unset($setting['channel'][$id - 1]);
			SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
		}

		return true;
	}
	
    public function addThirdPartyLoginChannel(array $config)
    {
		$setting = $this->getThirdPartyLoginSetting();
		$nameArr = array_column(array_column($setting['channel'], 'setting'), 'name');
		$index = array_search($config['setting']['name'], $nameArr);
		if ($index !== false) {
			throw new \RuntimeException('该授权方式名称已存在');
		}

		$setting['channel'][] = $config;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
	}
	
    public function updateThirdPartyLoginChannelById($id, array $config)
    {
		$setting = $this->getThirdPartyLoginSetting();
		if (empty($setting['channel'][$id - 1])) {
			throw new \RuntimeException('该授权方式不存在');
		}
		$nameArr = array_column($setting['channel'], 'name');
		$index = array_search($config['setting']['name'], $nameArr);
		if ($index !== false && $index != $id - 1) {
			throw new \RuntimeException('该授权方式名称已存在');
		}
		$setting['channel'][$id - 1] = $config;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
	}

	public function setDefaultLoginSetting(array $data)
	{
		$setting = $this->getThirdPartyLoginSetting();
		$setting['default_login_setting'] = $data;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
	}

	public function getDefaultLoginSetting()
	{
		$setting = $this->getThirdPartyLoginSetting();
		return $setting['default_login_setting'] ?? [];
	}
}
