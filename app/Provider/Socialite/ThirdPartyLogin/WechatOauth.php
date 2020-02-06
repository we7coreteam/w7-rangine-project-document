<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use Overtrue\Socialite\Providers\QQProvider;

class WechatOauth extends QQProvider
{
    use OauthTrait;

    public function getAppName()
    {
        return 'wechat';
    }

    protected function getAuthUrl($state)
    {
        return parent::getAuthUrl($state);
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
}