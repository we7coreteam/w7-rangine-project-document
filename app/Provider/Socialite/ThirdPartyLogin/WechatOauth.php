<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use Overtrue\Socialite\Providers\WeChatProvider;

class WechatOauth extends WeChatProvider
{
    use OauthTrait;

    public function getAppUnionId()
    {
        return '2';
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