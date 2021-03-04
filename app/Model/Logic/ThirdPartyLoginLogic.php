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
			//qq，微信为默认项
			$setting['channel'] = [
				'1' => [
					'is_default' => true,
					'setting' => [
						'name' => 'QQ',
						'logo' => 'http://cdn.w7.cc/web/resource/images/wechat/qqlogin.png'
					],
					'convert' => [
						'uid' => 'openid',
						'username' => 'nickname'
					]
				],
				'2' => [
					'is_default' => true,
					'setting' => [
						'name' => '微信',
						'logo' => 'http://cdn.w7.cc/web/resource/images/wechat/wxlogin.png'
					],
					'convert' => [
						'uid' => 'openid',
						'username' => 'nickname'
					]
				]
			];
			SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
		}
	}

	public function getThirdPartyLoginSetting($hide = 0)
	{
		$setting = SettingLogic::instance()->getByKey(self::THIRD_PARTY_LOGIN_SETTING_KEY, $hide);
		if (!$setting) {
			return [];
		}

		return $setting->setting;
	}

	public function getThirdPartyLoginChannelById($id, $hide = 0)
	{
		$setting = $this->getThirdPartyLoginSetting($hide);
		$setting = $setting['channel'][$id] ?? [];
		if ($setting) {
			//判断是不是默认支持的
			$setting['is_default'] = $setting['is_default'] ?? false;
		}

		return $setting;
	}

	public function deleteThirdPartyLoginChannelById($id)
	{
		$setting = $this->getThirdPartyLoginSetting();
		if (!empty($setting['channel'][$id])) {
			$loginSetting = $this->getDefaultLoginSetting();
			if (!empty($loginSetting['default_login_channel']) && $loginSetting['default_login_channel'] == $id) {
				$loginSetting['default_login_channel'] = '';
				$loginSetting['is_need_bind'] = '';
				$this->setDefaultLoginSetting($loginSetting);
			}
			unset($setting['channel'][$id]);
			SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
		}

		return true;
	}

	public function addThirdPartyLoginChannel(array $config)
	{
		$setting = $this->getThirdPartyLoginSetting();
		$maxId = max(array_keys($setting['channel'])) + 1;
		$setting['channel'][$maxId] = $config;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);
	}

	public function updateThirdPartyLoginChannelById($id, array $config)
	{
		$setting = $this->getThirdPartyLoginSetting();
		if (empty($setting['channel'][$id])) {
			throw new \RuntimeException('该授权方式不存在');
		}
		if (empty($config['convert'])) {
			$config['convert'] = $setting['channel'][$id]['convert'];
		}
		$config['is_default'] = $setting['channel'][$id]['is_default'] ?? false;
		$setting['channel'][$id] = $config;
		SettingLogic::instance()->save(self::THIRD_PARTY_LOGIN_SETTING_KEY, $setting);

		if (empty($config['setting']['enable'])) {
			$loginSetting = $this->getDefaultLoginSetting();
			if (!empty($loginSetting['default_login_channel']) && $loginSetting['default_login_channel'] == $id) {
				$loginSetting['default_login_channel'] = '';
				$this->setDefaultLoginSetting($loginSetting);
			}
		}
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
