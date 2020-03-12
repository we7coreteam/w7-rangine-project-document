<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use W7\App\Model\Logic\ThirdPartyLoginLogic;
use Overtrue\Socialite\User;
use RuntimeException;

trait OauthTrait
{
	protected $config;
	protected $convert;

	private function initConfig()
	{
		$config = iloader()->get(ThirdPartyLoginLogic::class)->getThirdPartyLoginChannelById($this->getAppUnionId());
		if (!$config['setting']) {
			throw new \RuntimeException('授权登陆方式 ' . $this->getAppUnionId() . ' 不存在');
		}
		$this->config = $config['setting'];
		$this->convert = $config['convert'] ?? [];
	}

	abstract public function getAppUnionId();

	/**
	 * Get the token URL for the provider.
	 *
	 * @return string
	 */
	protected function getTokenUrl()
	{
		$this->initConfig();
		return $this->config['access_token_url'];
	}

	protected function getUserInfoUrl()
	{
		$this->initConfig();
		return $this->config['user_info_url'];
	}

	/**
	 * Map the raw user array to a Socialite User instance.
	 *
	 * @param array $user
	 *
	 * @return \Overtrue\Socialite\User
	 */
	protected function mapUserToObject(array $user)
	{
		$this->initConfig();
		if (empty($this->convert)) {
			throw new RuntimeException('自定义授权转换配置错误');
		}
		$userConvert = [];
		foreach ($this->convert as $key => $value) {
			$userConvert[$key] = $this->arrayItem($user, $value);
		}
		return new User($userConvert);
	}
}