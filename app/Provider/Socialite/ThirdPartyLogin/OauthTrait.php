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

        $config = iloader()->get(ThirdPartyLoginLogic::class)->getThirdPartyLoginChannelById($this->getAppUnionId());
        if (!$config['setting']) {
            throw new \RuntimeException('授权登陆方式 ' . $this->getAppUnionId() . ' 不存在');
        }
        $this->config = $config['setting'];
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

    protected function getUserInfoUrl() {
        $this->initConfig();
        return $this->config['user_info_url'];
    }
}