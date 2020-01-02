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

use GuzzleHttp\Client;
use W7\App\Model\Entity\UserThirdParty;
use W7\Core\Helper\Traiter\InstanceTraiter;

class OauthLogic extends BaseLogic
{
	use InstanceTraiter;

	const TIMEOUNT = 30;

	private $thirdPartyLoginSetting;
	private $httpClient;

	public function __construct()
	{
		$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_THIRD_PARTY_LOGIN);
		if (empty($setting)) {
			$this->thirdPartyLoginSetting = [];
		} else {
			$this->thirdPartyLoginSetting = $setting->setting;
		}

		$this->httpClient = new Client([
			'timeout' => self::TIMEOUNT,
			'verify' => false,
			'http_errors' => false
		]);
	}

	public function getThirdPartyUserByUsernameUid($uid, $username) {
		return UserThirdParty::query()->where([
			'openid' => $uid,
			'username' => $username,
		])->first();
	}

	public function getLoginUrl() {
		if (empty($this->thirdPartyLoginSetting['enable'])) {
			return '';
		}

		$data = [
			'redirect' => '__redirect__',
			'appid' => $this->thirdPartyLoginSetting['app_id']
		];

		$response = $this->httpClient->post($this->thirdPartyLoginSetting['login_url_url'], [
			'form_params' => $data,
		]);

		$result = $response->getBody()->getContents();
		if (empty($result)) {
			return '';
		}

		$result = json_decode($result, true);
		if (!empty($result['error'])) {
			throw new \RuntimeException($result['error']);
		}

		return $result['url'];
	}

	public function getAccessToken($code) {
		if (empty($code)) {
			throw new \RuntimeException('Invalid code');
		}
		$data = [
			'code' => $code,
			'appid' => $this->thirdPartyLoginSetting['app_id']
		];

		$response = $this->httpClient->post($this->thirdPartyLoginSetting['access_token_url'], [
			'form_params' => $data,
		]);

		$result = $response->getBody()->getContents();

		if (empty($result)) {
			throw new \RuntimeException('Invalid code.');
		}

		$result = json_decode($result, true);
		if (!empty($result['error'])) {
			throw new \RuntimeException($result['error']);
		}

		return $result;
	}

	public function getUserInfo($accessToken) {
		if (empty($accessToken)) {
			throw new \RuntimeException('Invalid access token');
		}

		$data = [
			'access_token' => $accessToken,
			'appid' => $this->thirdPartyLoginSetting['app_id']
		];

		$response = $this->httpClient->post($this->thirdPartyLoginSetting['user_info_url'], [
			'form_params' => $data,
		]);

		$result = $response->getBody()->getContents();

		if (empty($result)) {
			throw new \RuntimeException('Invalid access token.');
		}

		$result = json_decode($result, true);
		if (!empty($result['error'])) {
			throw new \RuntimeException($result['error']);
		}

		return $result;
	}
}
