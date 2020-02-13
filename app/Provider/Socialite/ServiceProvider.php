<?php

namespace W7\App\Provider\Socialite;

use Overtrue\Socialite\SocialiteManager;
use Symfony\Component\Finder\Finder;
use W7\Core\Provider\ProviderAbstract;

class ServiceProvider extends ProviderAbstract
{
    public function register()
    {
        $this->registerSocialiteManager();
        $this->registerThirdPartyLogin();
    }

    private function registerSocialiteManager()
    {
        iloader()->set(SocialiteManager::class, function() {
            return new SocialiteManager([]);
        });
    }

    private function registerThirdPartyLogin()
    {
        $thirdPartyLogins = [];
		foreach ((new Finder)->in(__DIR__ . '/ThirdPartyLogin/')->files() as $file) {
			if ($file->getExtension() !== 'php') {
				continue;
			}

			if (strrchr($file->getFilename(), 'Trait') === false) {
				$fileName = substr($file->getBasename(), 0, -4);
				$thirdPartyLogins[] = "\\W7\\App\\Provider\\Socialite\\ThirdPartyLogin\\" . $fileName;
			}
		}

        /**
         * @var SocialiteManager $socialite
         */
        $socialite = iloader()->get(SocialiteManager::class);
		foreach ($thirdPartyLogins as $name => $thirdPartyLogin) {
            $obj = new $thirdPartyLogin($socialite->getRequest(), '', '', '');
            // if (!($obj instanceof ThirdPartyLoginAbstract)) {
            //     throw new \RuntimeException('class ' . $thirdPartyLogin . ' must instanceof ' . ThirdPartyLoginAbstract::class);
            // }
            $appId = $obj->getAppUnionId();
			$socialite->extend($appId, function ($config) use ($socialite, $thirdPartyLogin, $appId) {
                //测试用
                $redirectUrl = empty($config['redirect_url']) ? ienv('API_HOST') . 'login?app_id=' . $appId : $config['redirect_url'];
                if (ienv('OAUTH_TEST')) {
                    $redirectUrl = parse_url($redirectUrl, PHP_URL_QUERY)['redirect_url'] ?? '';
                    if ($appId == 1) {
                        $redirectUrl = 'https://s.w7.cc/v1/qq/userBack?is_passport=1&callback=' . ienv('API_HOST') . 'oauth/login?app_id=' . $appId . '&redirect_url=' . $redirectUrl;
                    } else if ($appId == 2) {
                        $redirectUrl = 'https://s.w7.cc/v1/wechatweb/callback?is_passport=1&callback=' . ienv('API_HOST') . 'oauth/login?app_id=' . $appId . '&redirect_url=' . $redirectUrl;
                    } else {
                        $redirectUrl = ienv('API_HOST') . 'login?app_id=' . $appId . '&redirect_url=' . $redirectUrl;
                    }
                }
                
                return new $thirdPartyLogin(
                    $socialite->getRequest(),
                    $config['client_id'],
                    $config['client_secret'],
                    $redirectUrl
                );
            });
		}
    }
}