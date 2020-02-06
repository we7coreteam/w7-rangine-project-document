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
		$setting = SettingLogic::instance()->getByKey(self::THIRD_PARTY_LOGIN_SETTING_KEY);
		if (empty($setting['channel'])) {
			$setting['channel'] = [
				[
					'app_union_key' => 'qq',
					'name' => 'QQ'
				],
				[
					'app_union_key' => 'wechat',
					'name' => '微信'
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

		return $setting;
	}

    public function getThirdPartyLoginChannel()
    {
		$setting = $this->getThirdPartyLoginSetting();
		$channel = $setting['channel'] ?? [];

		$channel = array_column($channel, 'name');
		foreach($channel as $key => $item) {
			$channel[$key]['id'] = $key + 1;
		}
		return $channel;
	}

    public function getThirdPartyLoginChannelByName($name)
    {
		$setting = $this->getThirdPartyLoginSetting();
		$nameArr = array_column($setting['channel'], 'name');
		$index = array_search($name, $nameArr);
		if ($index === false) {
			return false;
		}

		return $setting['channel'][$index];
	}

    public function getThirdPartyLoginChannelById($id)
    {
		$setting = $this->getThirdPartyLoginSetting();
		return $setting['channel'][$id - 1] ?? [];
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
		$nameArr = array_column($setting['channel'], 'name');
		$index = array_search($config['name'], $nameArr);
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
		$index = array_search($config['name'], $nameArr);
		if ($index !== false && $index != $id - 1) {
			throw new \RuntimeException('该授权方式名称已存在');
		}
		$setting['channel'][$id] = $config;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
	}

	public function setDefaultLoginChannel($channelName)
	{
		$setting = $this->getThirdPartyLoginSetting();
		$setting['default_login_channel'] = $channelName;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
	}

	public function getDefaultLoginChannel()
	{
		$setting = $this->getThirdPartyLoginSetting();
		return $setting['default_login_channel'] ?? 'default';
	}
}
