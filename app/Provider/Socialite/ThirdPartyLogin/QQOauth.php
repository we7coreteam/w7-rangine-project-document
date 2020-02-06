<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use Overtrue\Socialite\Providers\QQProvider;

class QQOauth extends QQProvider
{
    use OauthTrait;

    public function getAppName()
    {
        return 'qq';
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