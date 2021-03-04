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
use W7\App\Model\Entity\Setting;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserThirdParty;
use W7\App\Model\Logic\ThirdPartyLoginLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Core\Session\Session;
use W7\Http\Message\Server\Request;

class AuthController extends BaseController
{
	public function user(Request $request)
	{
		$userSession = $request->session->get('user');
		/**
		 * @var User $user
		 */
		$user = UserLogic::instance()->getByUid($userSession['uid']);
		if (!$user) {
			$request->session->destroy();
			throw new ErrorHttpException('请先登录', [], Setting::ERROR_NO_LOGIN);
		}

		$source = [
			'source_name' => '',
			'username' => ''
		];
		$userSourceAppId = $request->session->get('user-source-app');
		if ($userSourceAppId) {
			$userSource = UserThirdParty::query()->where('source', '=', $userSourceAppId)->where('uid', '=', $user->id)->first();
			$loginChannel = ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelById($userSourceAppId);
			$source = [
				'source_name' => $loginChannel['setting']['name'],
				'username' => $userSource->username
			];
		}
		$result = [
			'id' => $user->id,
			'username' => $user->username,
			'created_at' => $user->created_at->toDateTimeString(),
			'updated_at' => $user->updated_at->toDateTimeString(),
			//判断当前用户是否有密码
			'no_password' => empty($user->userpass),
			'user_source' => $source,
			'acl' => [
				'has_manage' => $user->isFounder
			]
		];

		return $this->data($result);
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

		$this->saveUserInfo($request->session, $user);

		return $this->data('success');
	}

	public function method(Request $request)
	{
		$redirectUrl = $request->post('redirect_url');
		$setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginSetting();
		$data = [];
		/**
		 * @var SocialiteManager $socialite
		 */
		$socialite = iloader()->get(SocialiteManager::class);
		//获取可用的第三方登录列表
		foreach ($setting['channel'] as $key => $item) {
			if (!empty($item['setting']['enable'])) {
				try {
					$socialite = clone $socialite;
					$url = ienv('API_HOST') . 'admin-login?app_id=' . $key . '&redirect_url=' . urlencode($redirectUrl);
					$redirect = $socialite->config(new Config([
						'client_id' => $item['setting']['app_id'],
						'client_secret' => $item['setting']['secret_key']
					]))->driver($key)->stateless()->redirect($url)->getTargetUrl();
				} catch (Throwable $e) {
					$redirect = null;
				}

				$data[] = [
					'id' => $key,
					'name' => $item['setting']['name'],
					'logo' => $item['setting']['logo'],
					'redirect_url' => $redirect
				];
			}
		}
		return $this->data($data);
	}

	public function defaultLoginUrl(Request $request)
	{
		$redirectUrl = $request->post('redirect_url');
		$defaultSetting = ThirdPartyLoginLogic::instance()->getDefaultLoginSetting();
		if (!empty($defaultSetting['default_login_channel']) && $setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelById($defaultSetting['default_login_channel'])) {
			/**
			 * @var SocialiteManager $socialite
			 */
			$socialite = iloader()->get(SocialiteManager::class);
			$socialite = clone $socialite;
			$url = ienv('API_HOST') . 'login?app_id=' . $defaultSetting['default_login_channel'] . '&redirect_url=' . $redirectUrl;
			try {
				return $this->data($socialite->config(new Config([
					'client_id' => $setting['setting']['app_id'],
					'client_secret' => $setting['setting']['secret_key']
				]))->driver($defaultSetting['default_login_channel'])->stateless()->redirect($url)->getTargetUrl());
			} catch (Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
		}

		return $this->data('');
	}

	/**
	 * @api {post} /common/auth/third-party-login 三方登陆
	 *
	 * @apiName third-party-login
	 * @apiGroup auth
	 *
	 * @apiParam {string} code
	 * @apiParam {string} app_id
	 *
	 * @apiSuccess {string} success
	 * @apiSuccess {string} is_need_bind 需要绑定用户 true
	 * @apiSuccess {string} has_login    已登录，需要确认是否切换 true
	 * @apiSuccess {string} change_token 已登录，切换token
	 */
	public function thirdPartyLogin(Request $request)
	{
		$code = $request->input('code');
		if (empty($code)) {
			throw new ErrorHttpException('Code码错误');
		}
		$appId = $request->input('app_id');
		if (empty($appId)) {
			throw new ErrorHttpException('app_id错误');
		}

		$setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelById($appId);
		if (!$setting) {
			throw new ErrorHttpException('不支持该授权方式');
		}
		/**
		 * @var SocialiteManager $socialite
		 */
		$socialite = iloader()->get(SocialiteManager::class);
		$driver = $socialite->config(new Config([
			'client_id' => $setting['setting']['app_id'],
			'client_secret' => $setting['setting']['secret_key']
		]))->driver($appId)->stateless();

		$user = $driver->user($driver->getAccessToken($code));
		//获取第三方数据
		$userInfo = [
			'uid' => $user->uid,
			'username' => $user->username
		];

		if (empty($userInfo['username']) || empty($userInfo['uid'])) {
			throw new ErrorHttpException('登录用户数据错误，请重试');
		}

		//创建用户和绑定关系
		$loginSetting = ThirdPartyLoginLogic::instance()->getDefaultLoginSetting();
		$thirdPartyUser = UserThirdParty::query()->where([
			'openid' => $userInfo['uid'],
			'username' => $userInfo['username'],
			'source' => $appId
		])->first();

		if (empty($thirdPartyUser)) {
			$thirdPartyUser = UserThirdParty::query()->create([
				'openid' => $userInfo['uid'],
				'username' => $userInfo['username'],
				'uid' => 0,
				'source' => $appId,
			]);
		}

		$username = $thirdPartyUser->username;
		//如果当前第三方用户绑定的用户为空，执行绑定操作
		if (empty($thirdPartyUser->uid)) {
			if (empty($loginSetting['is_need_bind'])) {
				//不需要绑定已有账户的话，直接创建新用户
				$username = 'tpl_' . $userInfo['username'] . $userInfo['uid'];
				$thirdPartyUser->uid = UserLogic::instance()->createBucket($username);
				$thirdPartyUser->save();
			} else {
				$username = $userInfo['username'];
				$thirdPartyUser->uid = 0;
			}
		}
		//已登陆的用户校验是否需要切换用户S
		$sessionUser = $request->session->get('user');
		if ($sessionUser) {
			$LoginUserthirdParty = UserThirdParty::query()
				->where('uid', $sessionUser['uid'])
				->where('source', $appId)->first();
			if ($LoginUserthirdParty) {
				//如果当前登陆账户，已经绑定了第三方-账户一致，返回成功，账户不一致，提示切换
				if ($thirdPartyUser->uid) {//商城绑了文档
					if ($thirdPartyUser->uid != $sessionUser['uid']) {
						//4如果登陆用户和当前访问用户不一致
						$changeToken = 'temp_user_info_4' . date('YmdHis') . round(1000, 9999);
						icache()->set($changeToken, ['third_party_user_id' => $thirdPartyUser->id], 60 * 15);
						return $this->data(['has_login' => 1, 'change_token' => $changeToken, 'message' => '当前登录账号非微擎账户绑定账号，是否继续登录？']);
					}
				} elseif (!$thirdPartyUser->uid) {
					//3文档绑了商城，商城没有绑文档-去登陆
					$sourceToken = 'temp_user_info_3_source' . date('YmdHis') . round(1000, 9999);
					icache()->set($sourceToken, ['third_party_user_id' => $thirdPartyUser->id, 'source' => $appId], 60 * 15);
					return $this->data(['has_login' => 3, 'source_token' => $sourceToken, 'message' => '当前登录账号非微擎账户绑定账号，是否继续登录？']);
				}
			} else {
				//1当前登陆账户，没有绑定第三方-登陆账户也没有绑定第三方。商城没有绑文档，文档没有绑商城
				if (!$thirdPartyUser->uid) {
					//1文档没有绑商城，商城没有绑文档，如果切入用户，没有绑定账户，当前用户也没有绑定
					$bindToken = 'temp_user_info_1_unbind_two' . date('YmdHis') . round(1000, 9999);
					icache()->set($bindToken, ['third_party_user_id' => $thirdPartyUser->id], 60 * 15);
					return $this->data(['has_login' => 2, 'bind_token' => $bindToken, 'message' => '是否绑定当前微擎账户于该登录账户？']);
				} else {
					//2文档没有绑商城，商城绑了文档-去切换
					$changeToken = 'temp_user_info_2' . date('YmdHis') . round(1000, 9999);
					icache()->set($changeToken, ['third_party_user_id' => $thirdPartyUser->id], 60 * 15);
					return $this->data(['has_login' => 1, 'change_token' => $changeToken, 'message' => '当前登录账号非微擎账户绑定账号，是否继续登录？']);
				}
			}
		}
		/*0如果文档没有登陆，没有绑账户去绑定
		---如果登陆了
		 *1文档没有绑商城，商城没有绑文档-去绑定
		 *2文档没有绑商城，商城绑了文档-去切换
		 *3文档绑了商城，商城没有绑文档-去切换-去登陆
		 *4文档绑了商城，商城绑了文档，用户不一致-去切换-致登陆
		 **/
		//2已登陆的用户校验是否需要切换用户E

		$ret = $this->data($this->setThirdPartySession($request, $thirdPartyUser));
		return $ret;
	}

	/**
	 * @api {post} /common/auth/ThirdPartyUserCacheIn 文档绑了商城，商城没有绑文档切用户
	 *
	 * @apiName ThirdPartyUserCacheIn
	 * @apiGroup auth
	 *
	 * @apiParam {string} bind_token 用于绑定的bind_token
	 */
	public function ThirdPartyUserCacheIn(Request $request)
	{
		$user = $request->session->get('user');
		if (!$user) {
			throw new ErrorHttpException('当前账户未登陆');
		}
		$changeToken = $request->input('source_token');
		if (empty($changeToken)) {
			throw new ErrorHttpException('source_token错误');
		}

		$data = icache()->get($changeToken);
		if (isset($data['third_party_user_id'])) {
			$thirdPartyUser = UserThirdParty::query()->find($data['third_party_user_id']);
			if ($thirdPartyUser) {
				$username = $thirdPartyUser->username;
				$localUser = [
					'app_id' => $thirdPartyUser->source,
					'uid' => $thirdPartyUser->uid,
					'third-party-uid' => $thirdPartyUser->id,
					'username' => $username,
				];

				$request->session->destroy();
				//记录第三方登录app_id
				$request->session->set('user-source-app', $thirdPartyUser->source);
				$loginSetting = ThirdPartyLoginLogic::instance()->getDefaultLoginSetting();
				//需要绑定已有账户
				if (!empty($loginSetting['is_need_bind']) && empty($thirdPartyUser->uid)) {
					//保存第三方用户信息，触发用户绑定
					$request->session->set('third-party-user', $localUser);
					return $this->data([
						'is_need_bind' => true
					]);
				} else {
					throw new ErrorHttpException('当前可以直接登陆');
				}
			}
		}
		throw new ErrorHttpException('source_token已过期');
	}

	/**
	 * @api {post} /common/auth/changeThirdPartyUser 切换用户
	 *
	 * @apiName changeThirdPartyUser
	 * @apiGroup auth
	 *
	 * @apiParam {string} change_token 用于切换的change_token
	 */
	public function changeThirdPartyUser(Request $request)
	{
		$user = $request->session->get('user');
		if (!$user) {
			throw new ErrorHttpException('当前账户未登陆');
		}
		$changeToken = $request->input('change_token');
		if (empty($changeToken)) {
			throw new ErrorHttpException('change_token错误');
		}
		$data = icache()->get($changeToken);
		if (isset($data['third_party_user_id'])) {
			$thirdPartyUser = UserThirdParty::query()->find($data['third_party_user_id']);
			if ($thirdPartyUser) {
				return $this->data($this->setThirdPartySession($request, $thirdPartyUser));
			}
		}
		throw new ErrorHttpException('change_token已过期');
	}

	/**
	 * @api {post} /common/auth/bindThirdPartyUser 切换用户
	 *
	 * @apiName bindThirdPartyUser
	 * @apiGroup auth
	 *
	 * @apiParam {string} bind_token 用于绑定的bind_token
	 */
	public function bindThirdPartyUser(Request $request)
	{
		$user = $request->session->get('user');
		if (!$user) {
			throw new ErrorHttpException('当前账户未登陆');
		}
		$changeToken = $request->input('bind_token');
		if (empty($changeToken)) {
			throw new ErrorHttpException('bind_token错误');
		}

		$data = icache()->get($changeToken);
		if (isset($data['third_party_user_id'])) {
			$thirdPartyUser = UserThirdParty::query()->find($data['third_party_user_id']);
			if ($thirdPartyUser) {
				if (!$thirdPartyUser->uid) {
					$thirdPartyUser->uid = $user['uid'];
					$thirdPartyUser->save();
					return $this->data($this->setThirdPartySession($request, $thirdPartyUser));
				}
			}
		}
		throw new ErrorHttpException('bind_token已过期');
	}

	public function setThirdPartySession(Request $request, $thirdPartyUser)
	{
		$username = $thirdPartyUser->username;
		$localUser = [
			'app_id' => $thirdPartyUser->source,
			'uid' => $thirdPartyUser->uid,
			'third-party-uid' => $thirdPartyUser->id,
			'username' => $username,
		];

		$request->session->destroy();
		//记录第三方登录app_id
		$request->session->set('user-source-app', $thirdPartyUser->source);
		$loginSetting = ThirdPartyLoginLogic::instance()->getDefaultLoginSetting();
		//需要绑定已有账户
		if (!empty($loginSetting['is_need_bind']) && empty($thirdPartyUser->uid)) {
			//保存第三方用户信息，触发用户绑定
			$request->session->set('third-party-user', $localUser);
			return [
				'is_need_bind' => true
			];
		} else {
			$request->session->set('user-source-app', $thirdPartyUser->source);
			$this->saveUserInfo($request->session, $thirdPartyUser->bindUser);
			return 'success';
		}
	}

	/**
	 * @api {post} /common/auth/third-party-login-bind 绑定用户
	 *
	 * @apiName third-party-login-bind
	 * @apiGroup auth
	 *
	 * @apiParam {string} handle 操作类型bind：绑定 reg:注册
	 * @apiParam {string} username 用户名
	 * @apiParam {string} userpass 密码
	 */
	public function thirdPartyLoginBind(Request $request)
	{
		$data = $this->validate($request, [
			'handle' => 'required',
			'username' => 'required',
			'userpass' => 'required'
		], [
			'handle.required' => '操作不能为空',
			'username.required' => '用户名不能为空',
			'userpass.required' => '密码不能为空'
		]);
		$thirdPartyUser = $request->session->get('third-party-user');
		if (!$thirdPartyUser) {
			throw new ErrorHttpException('用户信息已过期请重新登陆', [], Setting::ERROR_NO_LOGIN);
		}

		$handle = $request->input('handle', 'bind');

		$UserThirdParty = UserThirdParty::query()->find($thirdPartyUser['third-party-uid']);
		if (!$UserThirdParty) {
			throw new ErrorHttpException('请先授权');
		}

		if ($UserThirdParty->uid) {
			throw new ErrorHttpException('用户信息已过期请重新登陆', [], Setting::ERROR_NO_LOGIN);
		}

		$msg = '注册成功';
		if ($handle == 'bind') {
			$msg = '绑定成功';
			//绑定已有用户
			$user = UserLogic::instance()->getByUserName($data['username']);
			if (empty($user)) {
				throw new ErrorHttpException('用户名或密码错误，请检查');
			}

			if ($user->userpass != UserLogic::instance()->userPwdEncryption($user->username, $data['userpass'])) {
				throw new ErrorHttpException('用户名或密码错误，请检查');
			}

			$userThirdPartyHas = UserThirdParty::query()->where('uid', $user->id)->first();
			if ($userThirdPartyHas) {
				//如果当前用户
				throw new ErrorHttpException('当前账号已绑定其它账号，您可以选择重新注册或绑定其它账号。');
			}

			if (!empty($user->is_ban)) {
				throw new ErrorHttpException('您使用的用户已经被禁用，请联系管理员');
			}
		} else {//'reg'
			//新增
			$data = [
				'username' => trim($data['username']),
				'userpass' => trim($data['userpass']),
			];
			$data['remark'] = $request->input('remark', '');

			try {
				$userId = UserLogic::instance()->createUser($data);
				$user = User::query()->find($userId);
			} catch (\Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
		}

		$UserThirdParty->update([
			'uid' => $user->id,
		]);

		$request->session->destroy();
		//记录第三方登录app_id
		$request->session->set('user-source-app', $thirdPartyUser['app_id']);
		$this->saveUserInfo($request->session, $user);

		return $this->data($msg);
	}

	/**
	 * 解绑
	 */
	public function unbind(Request $request)
	{
		$userSession = $request->session->get('user');
		$userSourceAppId = $request->session->get('user-source-app');
		$res = UserThirdParty::query()
			->where('source', '=', $userSourceAppId)
			->where('uid', '=', $userSession['uid'])
			->update(['uid' => 0]);
		if ($res) {
			//$this->logout($request);
			return $this->data($res);
		}
		throw new ErrorHttpException('解绑失败');
	}

	public function logout(Request $request)
	{
		$sourceApp = $request->session->get('user-source-app');
		$request->session->destroy();
		if ($sourceApp) {
			$setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelById($sourceApp);
			if (!$setting) {
				throw new ErrorHttpException('不支持该授权方式');
			}
			/**
			 * @var SocialiteManager $socialite
			 */
			$socialite = iloader()->get(SocialiteManager::class);
			return $socialite->config(new Config([
				'client_id' => $setting['setting']['app_id'],
				'client_secret' => $setting['setting']['secret_key']
			]))->driver($sourceApp)->logout($this->response());
		} else {
			$utl = ienv('API_HOST') . 'admin-login';
			return $this->response()->redirect($utl);
		}
	}

	public function getlogouturl(Request $request)
	{
		$utl = ienv('API_HOST') . 'common/auth/logout';
		return $this->data($utl);
	}

	private function saveUserInfo(Session $session, $user)
	{
		$session->set('user', [
			'uid' => $user->id,
			'username' => $user->username,
		]);
		//用户在修改密码后，删除该值，触发退出操作
		icache()->set(sprintf(UserLogic::USER_LOGOUT_AFTER_CHANGE_PWD, $user->id), 1, 7 * 86400);
	}
}
