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

namespace W7\App\Provider\Socialite\ThirdPartyLogin;

use GuzzleHttp\Client;
use Overtrue\Socialite\ProviderInterface;
use Overtrue\Socialite\Providers\AbstractProvider;
use Overtrue\Socialite\AccessTokenInterface;
use W7\Http\Message\Server\Response;

class We7Oauth extends AbstractProvider implements ProviderInterface
{
    use OauthTrait;

    public function getAppUnionId()
    {
        return '3';
    }

    protected function getAuthUrl($state)
    {
        $data = [
            'redirect' => $this->redirectUrl,
            'appid' => $this->clientId
        ];

        $headers = [];
        if (ienv('OAUTH_USER_AGENT')) {
            $headers['User-Agent'] = ienv('OAUTH_USER_AGENT');
        }
        $response = (new Client())->post('http://api.w7.cc/oauth/login-url/index', [
            'form_params' => $data,
            'headers' => $headers
        ]);

        $result = $response->getBody()->getContents();
        if (empty($result)) {
            throw new \RuntimeException('获取授权地址错误');
        }

        $result = json_decode($result, true);
        if (!empty($result['error'])) {
            throw new \RuntimeException($result['error']);
        }

        return $result['url'];
    }

    /**
     * Get the Post fields for the token request.
     *
     * @param string $code
     *
     * @return array
     */
    protected function getTokenFields($code)
    {
        $data = [
            'appid' => $this->clientId,
            'code' => $code
        ];
        $data['sign'] = $this->getSign($data, $this->clientSecret);
        return $data;
    }

    public function getSign($data, $appsecret = '')
    {
        unset($data['sign']);

        ksort($data, SORT_STRING);
        reset($data);

        $sign = md5(http_build_query($data, '', '&') . $appsecret);
        return $sign;
    }

    /**
     * Get the access token for the given code.
     *
     * @param string $code
     *
     * @return \Overtrue\Socialite\AccessToken
     */
    public function getAccessToken($code)
    {
        $formParams = $this->getTokenFields($code);
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $formParams,
        ]);

        $data = \json_decode($response->getBody()->getContents(), true);
        $data['access_token'] = $data['accessToken'];
        return $this->parseAccessToken(\json_encode($data));
    }

    /**
     * Get the raw user for the given access token.
     * @param AccessTokenInterface $token
     * @return mixed
     */
    protected function getUserByToken(AccessTokenInterface $token)
    {
        $data = [
            'access_token' => $token->getToken()
        ];

        $response = $this->getHttpClient()->post($this->getUserInfoUrl(), [
            'form_params' => $data
        ]);

        return \json_decode($response->getBody()->getContents(), true);
    }

    public function logout(Response $psrResponse): Response
    {
        $data = [
            'redirect_url' => ienv('API_HOST') . 'admin-login'
        ];

        $headers = [];
        if (ienv('OAUTH_USER_AGENT')) {
            $headers['User-Agent'] = ienv('OAUTH_USER_AGENT');
        }
        $response = (new Client())->post('http://api.w7.cc/oauth/logout-url/index', [
            'form_params' => $data,
            'headers' => $headers
        ]);

        $result = $response->getBody()->getContents();
        if (empty($result)) {
            throw new \RuntimeException('获取退出授权地址错误');
        }

        $result = json_decode($result, true);
        if (!empty($result['error'])) {
            throw new \RuntimeException($result['error']);
        }

        return $psrResponse->redirect($result['url']);
    }
}
