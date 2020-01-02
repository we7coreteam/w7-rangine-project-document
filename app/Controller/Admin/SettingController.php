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

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\SettingLogic;
use W7\Http\Message\Server\Request;

class SettingController extends BaseController
{
	private $handler = [
		SettingLogic::KEY_COS => 'saveCos',
		SettingLogic::KEY_THIRD_PARTY_LOGIN => 'saveThirdPartyLogin',
	];

	public function cos(Request $request) {
		$this->check($request);

		$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_COS);
		return $this->data([
			'key' => SettingLogic::KEY_COS,
			'setting' => $setting->setting,
		]);
	}

	public function thirdPartyLogin(Request $request) {
		$this->check($request);

		$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_THIRD_PARTY_LOGIN);
		return $this->data([
			'key' => SettingLogic::KEY_THIRD_PARTY_LOGIN,
			'setting' => $setting->setting,
		]);
	}

	public function save(Request $request)
	{
		$this->validate($request, [
			'key' => 'required',
		], [
			'key.required' => 'key必填',
		]);

		$key = $request->post('key');
		if (!isset($this->handler[$key])) {
			throw new ErrorHttpException('错误的配置key');
		}

		if (!empty($this->handler[$key])) {
			$value = call_user_func_array([$this, $this->handler[$key]], [$request]);
		} else {
			$value = $request->post('setting');
		}

		SettingLogic::instance()->save($key, $value);
		return $this->data('success');
	}

	private function saveCos(Request $request) {
		$this->validate($request, [
			'setting.app_id' => 'required',
			'setting.secret_id' => 'required',
			'setting.secret_key' => 'required',
			'setting.bucket' => 'required',
			'setting.region' => 'required',
			'setting.url' => 'sometimes|url',
			'setting.path' => 'sometimes|regex:/^\/[a-zA-Z\-_0-9]+$/i'
		], [
			'setting.app_id.required' => 'app_id必填',
			'setting.secret_id.required' => 'secret_id必填',
			'setting.secret_key.required' => 'secret_key必填',
			'setting.bucket.required' => 'bucket必填',
			'setting.region.required' => '所属地址必填',
			'setting.url.url' => '附件访问域名格式错误',
			'setting.path.regex' => '保存目录填写错误，格式例如：/savepath '
		]);

		$setting = $request->post('setting');

		$data = [
			'app_id' => $setting['app_id'],
			'secret_id' => $setting['secret_id'],
			'secret_key' => $setting['secret_key'],
			'bucket' => $setting['bucket'],
			'region' => $setting['region'],
			'url' => rtrim($setting['url'], '/'),
			'path' => rtrim($setting['path'], '/'),
		];

		if (empty($data['path'])) {
			$data['path'] = '';
		}

		return $data;
	}

	private function saveThirdPartyLogin(Request $request) {
		$this->validate($request, [
			'setting.app_id' => 'required',
			'setting.secret_key' => 'required',
			'setting.access_token_url' => 'required|url',
			'setting.user_info_url' => 'required|url',
			'setting.login_url_url' => 'required|url',
		], [
			'setting.app_id.required' => 'app_id必填',
			'setting.secret_key.required' => 'secret_key必填',
			'setting.access_token_url.url' => '获取access_token接口地址错误',
			'setting.user_info_url.url' => '获取用户信息接口地址错误',
			'setting.login_url_url.url' => '获取登录地址接口地址错误',
		]);

		$setting = $request->post('setting');

		$data = [
			'app_id' => $setting['app_id'],
			'secret_key' => $setting['secret_key'],
			'user_info_url' => rtrim($setting['user_info_url'], '/'),
			'access_token_url' => rtrim($setting['access_token_url'], '/'),
			'login_url_url' => rtrim($setting['login_url_url'], '/'),
			'enable' => !empty($setting['enable']) ? true : false,
		];


		return $data;
	}

	private function check(Request $request) {
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}
}
