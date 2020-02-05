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

    public function thirdPartyLoginChannel(Request $request) 
    {
		$this->check($request);
		return $this->data(ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannel());
    }
    
    public function getThirdPartyLoginChannelById(Request $request)
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
	
	public function saveThirdPartyLogin(Request $request) 
    {
		$this->check($request);
		$params = $this->validate($request, [
			'name' => 'required',
			'logo' => 'required|url',
			'app_id' => 'required',
			'secret_key' => 'required',
			'access_token_url' => 'required|url',
			'user_info_url' => 'required|url',
			'login_url_url' => 'required|url',
		], [
			'name.required' => 'name必填',
			'logo.required' => 'logo必传',
			'app_id.required' => 'app_id必填',
			'secret_key.required' => 'secret_key必填',
			'access_token_url.url' => '获取access_token接口地址错误',
			'user_info_url.url' => '获取用户信息接口地址错误',
			'login_url_url.url' => '获取登录地址接口地址错误',
		]);
		$params['user_info_url'] = rtrim($params['user_info_url'], '/');
		$params['access_token_url'] = rtrim($params['access_token_url'], '/');
		$params['login_url_url'] = rtrim($params['login_url_url'], '/');
		$params['enable'] = !empty($params['enable']) ? true : false;
		
		try {
			ThirdPartyLoginLogic::instance()->addThirdPartyLoginChannel($params);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
    }

    public function updateThirdPartyLoginChannelById(Request $request)
    {
        $this->check($request);
        $params = $this->validate($request, [
			'id' => 'required',
			'name' => 'required',
			'logo' => 'required|url',
			'app_id' => 'required',
			'secret_key' => 'required',
			'access_token_url' => 'required|url',
			'user_info_url' => 'required|url',
			'login_url_url' => 'required|url',
		], [
			'name.required' => 'name必填',
			'logo.required' => 'logo必传',
			'app_id.required' => 'app_id必填',
			'secret_key.required' => 'secret_key必填',
			'access_token_url.url' => '获取access_token接口地址错误',
			'user_info_url.url' => '获取用户信息接口地址错误',
			'login_url_url.url' => '获取登录地址接口地址错误',
		]);
		$params['user_info_url'] = rtrim($params['user_info_url'], '/');
		$params['access_token_url'] = rtrim($params['access_token_url'], '/');
		$params['login_url_url'] = rtrim($params['login_url_url'], '/');
		$params['enable'] = !empty($params['enable']) ? true : false;
        try {
            return $this->data(ThirdPartyLoginLogic::instance()->updateThirdPartyLoginChannelById($params['id'], $params));
        } catch (\Throwable $e) {
            throw new ErrorHttpException($e->getMessage());
        }
	}

	public function deleteThirdPartyLoginChannelById(Request $request)
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
    
    public function setDefaultLoginChannel(Request $request) {
        $this->check($request);
		$params = $this->validate($request, [
			'default_login_channel' => 'required'
		]);

		ThirdPartyLoginLogic::instance()->setDefaultLoginChannel($params['default_login_channel']);
		return $this->data('success');
    }
    
    public function getDefaultLoginChannel(Request $request) {
		$this->check($request);
		
		return $this->data(ThirdPartyLoginLogic::instance()->getDefaultLoginChannel());
    }
}
