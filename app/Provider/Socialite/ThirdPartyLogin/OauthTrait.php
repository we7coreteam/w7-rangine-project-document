<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use W7\App\Model\Logic\ThirdPartyLoginLogic;

trait OauthTrait
{
    protected $config;

    private function initConfig() {
        if ($this->config) {
            return true;
        }

        $config = iloader()->get(ThirdPartyLoginLogic::class)->getThirdPartyLoginChannelByName($this->getAppName());
        if (!$config) {
            throw new \RuntimeException('授权登陆方式 ' . $this->getAppName() . ' 不存在');
        }
        $this->config = $config;
    }

    abstract public function getAppName();

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        $this->initConfig();
        return $this->buildAuthUrlFromBase($this->config['login_url_url'], $state);
    }

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

    protected function getUserInfoUrl() {
        $this->initConfig();
        return $this->config['user_info_url'];
    }
}