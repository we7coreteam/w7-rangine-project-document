<?php

namespace W7\App\Provider\Socialite\ThirdPartyLogin;
use Overtrue\Socialite\AccessTokenInterface;
use Overtrue\Socialite\User;

class We7OauthLogin extends ThirdPartyLoginAbstract
{
    public function getAppName()
    {
        return 'we7';
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
        return [
            'code' => $code
        ];
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
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        return $this->parseAccessToken($response->getBody()->getContents());
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param \Overtrue\Socialite\AccessTokenInterface $token
     *
     * @return array
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

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     *
     * @return \Overtrue\Socialite\User
     */
    protected function mapUserToObject(array $user)
    {
        return new User([
            'openid' => $this->arrayItem($user, 'uid'),
            'username' => $this->arrayItem($user, 'username')
        ]);
    }
}