<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use Overtrue\Socialite\ProviderInterface;
use Overtrue\Socialite\Providers\AbstractProvider;
use Symfony\Component\HttpFoundation\Request;
use W7\App\Model\Logic\ThirdPartyLoginLogic;

abstract class ThirdPartyLoginAbstract extends AbstractProvider implements ProviderInterface
{
    protected $config;

    public function __construct(Request $request = null, $clientId = null, $clientSecret = null, $redirectUrl = null)
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);
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
        return $this->buildAuthUrlFromBase($this->config['login_url_url'], $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return $this->config['access_token_url'];
    }

    protected function getUserInfoUrl() {
        return $this->config['user_info_url'];
    }
}