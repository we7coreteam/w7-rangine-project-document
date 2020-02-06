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

namespace W7\App\Controller\Common;

use Overtrue\Socialite\Config;
use Overtrue\Socialite\SocialiteManager;
use Throwable;
use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserThirdParty;
use W7\App\Model\Logic\OauthLogic;
use W7\App\Model\Logic\ThirdPartyLoginLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Http\Message\Server\Request;

class AuthController extends BaseController
{
	public function test(Request $request)
	{
		/**
		 * @var SocialiteManager $socialite
		 */
		$socialite = iloader()->get(SocialiteManager::class);
		return $this->response()->redirect($socialite->config(new Config([
			'client_id' => 'wa84a4166e8e1f471a',
			'client_secret' => ''
		]))->driver('we7')->stateless()->redirect()->getTargetUrl());
	}

	public function login(Request $request)
	{
		$data = $this->validate($request, [
			'username' => 'required',
			'userpass' => 'required',
			'code' => 'required',
		], [
			'username.required' => '用户名不能为空',
			'userpass.required' => '密码不能为空',
			'code.required' => '验证码不能为空',
		]);
		$code = $request->session->get('img_code');
		if (strtolower($data['code']) != strtolower($code)) {
			throw new ErrorHttpException('请输入正确的验证码');
		}

		$user = UserLogic::instance()->getByUserName($data['username']);
		if (empty($user)) {
			throw new ErrorHttpException('用户名或密码错误，请检查');
		}

		if ($user->userpass != UserLogic::instance()->userPwdEncryption($user->username, $data['userpass'])) {
			throw new ErrorHttpException('用户名或密码错误，请检查');
		}

		if (!empty($user->is_ban)) {
			throw new ErrorHttpException('您使用的用户已经被禁用，请联系管理员');
		}

		$request->session->destroy();

		$request->session->set('user', [
			'uid' => $user->id,
			'username' => $user->username,
		]);

		return $this->data('success');
	}

	public function logout(Request $request)
	{
		$request->session->destroy();
		return $this->data('success');
	}

	public function method(Request $request) {
		$setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginSetting();
		$channel = array_column(array_column($setting['channel'], 'setting'), 'name');
		$data = [];

		/**
		 * @var SocialiteManager $socialite
		 */
		$socialite = iloader()->get(SocialiteManager::class);
		//获取可用的第三方登录列表
		foreach($channel as $key => $name) {
			if (!empty($setting['channel'][$key]['setting']['enable'])) {
				$data[$key]['id'] = $key + 1;
				$data[$key]['name'] = $name;
				$data[$key]['logo'] = $setting['channel'][$key]['setting']['logo'];
				
				try{
					$driver = $socialite->config(new Config([
						'client_id' =>  $setting['channel'][$key]['setting']['app_id'],
						'client_secret' =>  $setting['channel'][$key]['setting']['app_secret']
					]))->driver($name)->stateless();
					$data[$key]['redirect_url'] = $driver->redirect()->getTargetUrl();
				} catch(Throwable $e) {
					$data[$key]['redirect_url'] = '';
				}
			}
		}
		return $this->data($data);
	}

	public function user(Request $request)
	{
		$userSession = $request->session->get('user');
		/**
		 * @var User $user
		 */
		$user = UserLogic::instance()->getByUid($userSession['uid']);
		if (!$user) {
			$request->session->destroy();
			throw new ErrorHttpException('请先登录', [], 444);
		}

		$result = [
			'id' => $user->id,
			'username' => $user->username,
			'created_at' => $user->created_at->toDateTimeString(),
			'updated_at' => $user->updated_at->toDateTimeString(),
			'acl' => [
				'has_manage' => $user->isFounder
			]
		];

		return $this->data($result);
	}

	public function thirdPartyLogin(Request $request) {
		$code = $request->post('code');
		if (empty($code)) {
			throw new ErrorHttpException('Code码错误');
		}
		$type = $request->input('type');
		if (empty($code)) {
			throw new ErrorHttpException('type错误');
		}

		$config = ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelByName($type);
		if (!$config) {
			throw new ErrorHttpException('不支持该授权方式');
		}
		/**
		 * @var SocialiteManager $socialite
		 */
		$socialite = iloader()->get(SocialiteManager::class);
		$driver = $socialite->config(new Config([
			'client_id' => $config['app_id'],
			'client_secret' => $config['app_secret']
		]))->driver($type)->stateless();

		$user = $driver->user($driver->getAccessToken($code));
		//添加QQ用户数据
		$userInfo = $user->getOriginal();
		if (empty($userInfo['username']) || empty($userInfo['uid'])) {
			throw new ErrorHttpException('登录用户数据错误，请重试');
		}

		$user = OauthLogic::instance()->getThirdPartyUserByUsernameUid($userInfo['uid'], $userInfo['username']);
		if (empty($user)) {
			$localUsername = 'tpl_' . $userInfo['username'] . $userInfo['uid'];

			$uid = UserLogic::instance()->createBucket($localUsername);
			UserThirdParty::query()->create([
				'openid' => $userInfo['uid'],
				'username' => $userInfo['username'],
				'uid' => $uid,
				'source' => 1,
			]);

			$localUser = [
				'uid' => $uid,
				'username' => $localUsername,
			];
		} else {
			$localUser = [
				'uid' => $user->bindUser->id,
				'username' => $user->bindUser->username,
			];
		}

		$request->session->destroy();
		$request->session->set('user', $localUser);

		return $this->data('success');
	}
}
