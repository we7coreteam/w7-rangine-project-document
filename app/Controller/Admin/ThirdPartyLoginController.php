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
use W7\App\Model\Logic\ThirdPartyLoginLogic;
use W7\Http\Message\Server\Request;

class ThirdPartyLoginController extends BaseController
{
	private function check(Request $request)
	{
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}

	public function all(Request $request)
	{
		$this->check($request);

		$setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginSetting();
		$data = [];
		foreach ($setting['channel'] as $key => $item) {
			$data[] = [
				'id' => $key,
				'name' => $item['setting']['name'],
				'enable' => $item['setting']['enable'] ?? false
			];
		}
		return $this->data($data);
	}

	public function getById(Request $request)
	{
		$this->check($request);
		$params = $this->validate($request, [
			'id' => 'required'
		]);
		try {
			return $this->data(ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelById($params['id']));
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
	
	public function add(Request $request)
	{
		$this->check($request);
		$params = $this->validate($request, [
			'setting.name' => 'required',
			'setting.logo' => 'required|url',
			'setting.app_id' => 'required',
			'setting.secret_key' => 'required',
			'setting.access_token_url' => 'required|url',
			'setting.user_info_url' => 'required|url'
		], [
			'setting.name.required' => 'name必填',
			'setting.logo.required' => 'logo必传',
			'setting.app_id.required' => 'app_id必填',
			'setting.secret_key.required' => 'secret_key必填'
		]);
		$params['setting']['user_info_url'] = rtrim($params['setting']['user_info_url'], '/');
		$params['setting']['access_token_url'] = rtrim($params['setting']['access_token_url'], '/');
		$params['setting']['enable'] = !empty($request->post('setting')['enable']) ? 1 : 0;
		$params['convert'] = $request->post('convert');
		
		try {
			ThirdPartyLoginLogic::instance()->addThirdPartyLoginChannel($params);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function updateById(Request $request)
	{
		$this->check($request);
		$params = $this->validate($request, [
			'id' => 'required',
			'setting.name' => 'required',
			'setting.logo' => 'required|url',
			'setting.app_id' => 'required',
			'setting.secret_key' => 'required',
			'setting.access_token_url' => 'required|url',
			'setting.user_info_url' => 'required|url',
		], [
			'setting.name.required' => 'name必填',
			'setting.logo.required' => 'logo必传',
			'setting.app_id.required' => 'app_id必填',
			'setting.secret_key.required' => 'secret_key必填'
		]);
		$params['setting']['user_info_url'] = rtrim($params['setting']['user_info_url'], '/');
		$params['setting']['access_token_url'] = rtrim($params['setting']['access_token_url'], '/');
		$params['setting']['enable'] = !empty($request->post('setting')['enable']) ? 1 : 0;
		$params['convert'] = $request->post('convert');
		
		try {
			ThirdPartyLoginLogic::instance()->updateThirdPartyLoginChannelById($params['id'], $params);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function deleteById(Request $request)
	{
		$this->check($request);
		$params = $this->validate($request, [
			'id' => 'required',
		]);
		try {
			ThirdPartyLoginLogic::instance()->deleteThirdPartyLoginChannelById($params['id']);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function setDefaultLoginChannel(Request $request)
	{
		$this->check($request);

		$defaultLoginChannel = $request->post('default_login_channel', '');
		$isNeedBind = $request->post('is_need_bind', '');
		$isNeedBind = $isNeedBind == 1 ? true : false;
		ThirdPartyLoginLogic::instance()->setDefaultLoginSetting([
			'default_login_channel' => $defaultLoginChannel,
			'is_need_bind' => $isNeedBind
		]);
		return $this->data('success');
	}

	public function getDefaultLoginChannel(Request $request)
	{
		$this->check($request);
		
		return $this->data(ThirdPartyLoginLogic::instance()->getDefaultLoginSetting());
	}
}
