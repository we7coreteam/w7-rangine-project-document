<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use Overtrue\Socialite\Providers\QQProvider;
use Overtrue\Socialite\AccessTokenInterface;

class QQOauth extends QQProvider
{
	use OauthTrait;

	public function getAppUnionId()
	{
		return '1';
	}

	/**
	 * Get the token URL for the provider.
	 *
	 * @return string
	 */
	protected function getTokenUrl()
	{
		return parent::getTokenUrl();
	}

	protected function getUserByToken(AccessTokenInterface $token)
	{
		$url = $this->baseUrl.'/oauth2.0/me?access_token='.$token->getToken();
		$this->withUnionId && $url .= '&unionid=1';

		$response = $this->getHttpClient()->get($url);

		$me = json_decode($this->removeCallback($response->getBody()->getContents()), true);
		$this->openId = $me['openid'];
		$this->unionId = isset($me['unionid']) ? $me['unionid'] : '';

		$queries = [
			'access_token' => $token->getToken(),
			'openid' => $this->openId,
			'oauth_consumer_key' => $this->clientId,
		];

		$response = $this->getHttpClient()->get($this->baseUrl.'/user/get_user_info?'.http_build_query($queries));
		$info = json_decode($this->removeCallback($response->getBody()->getContents()), true);
		$info['openid'] = $this->openId;
		return $info;
	}
}