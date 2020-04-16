<?php

namespace W7\App\Handler\Exception;

use function GuzzleHttp\Psr7\build_query;
use Overtrue\Socialite\Config;
use Overtrue\Socialite\SocialiteManager;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\ThirdPartyLoginLogic;
use W7\Core\Exception\Handler\HandlerAbstract;
use W7\Core\Exception\RouteNotAllowException;
use W7\Core\Exception\RouteNotFoundException;
use W7\Core\Exception\ValidatorException;
use W7\Core\Session\Session;
use W7\Core\View\View;
use W7\Http\Message\Server\Response;

class ExceptionHandler extends HandlerAbstract
{
	public function handle(\Throwable $e) : Response
	{
		if ($e instanceof RouteNotFoundException || $e instanceof RouteNotAllowException) {
			$request = icontext()->getRequest();
			$route = $request->getUri()->getPath();
			if ($request->getQueryParams()) {
				$route .= '?' . build_query($request->getQueryParams());
			}
			//如果访问的是admin下的路由，先检测是否登录
			if (substr($route, 0, 12) == '/admin-login') {
				return $this->getResponse()->html(iloader()->singleton(View::class)->render('@public/index'));
			}
			if (substr($route, 0, 6) == '/admin') {
				$session = new Session();
				$session->start($request);
				if (!$session->get('user')) {
					$this->getResponse()->redirect((string)$this->getLoginUrl());
				}
			}
			//如果是访问预览的连接，判断该文档是否需要登录后预览
			if (substr($route, 0, 8) === '/chapter') {
				$session = new Session();
				$session->start($request);
				if (!$session->get('user')) {
					$documentId = explode('/', $route)[2] ?? '';
					$documentId = explode('?', $documentId)[0];
					$document = DocumentLogic::instance()->getById($documentId);
					//非公有文档，自动跳转登录
					if ($document && $document->is_public != Document::PUBLIC_DOCUMENT) {
						$this->getResponse()->redirect($this->getLoginUrl(ienv('API_HOST') . ltrim($route, '/')));
					}
				}
			}
			return $this->getResponse()->html(iloader()->singleton(View::class)->render('@public/index'));
		}

		if ($e instanceof ValidatorException) {
			$e = new ErrorHttpException($e->getMessage(), [], $e->getCode());
		}
		return parent::handle($e);
	}

	private function getLoginUrl($redirectUrl = null)
	{
		$defaultLoginChannel = ThirdPartyLoginLogic::instance()->getDefaultLoginSetting();
		if (empty($defaultLoginChannel['default_login_channel'])) {
			return '/login?redirect_url=' . urlencode($redirectUrl);
		} else {
			$setting = ThirdPartyLoginLogic::instance()->getThirdPartyLoginChannelById($defaultLoginChannel['default_login_channel']);
			if (!$setting) {
				return '/login?redirect_url=' . urlencode($redirectUrl);
			} else {
				/**
				 * @var SocialiteManager $socialite
				 */
				$socialite = iloader()->get(SocialiteManager::class);
				return $socialite->config(new Config([
					'client_id' => $setting['setting']['app_id'],
					'client_secret' => $setting['setting']['secret_key'],
					'redirect_url' => ienv('API_HOST') . 'login?app_id=' . $defaultLoginChannel['default_login_channel'] . '&redirect_url=' . $redirectUrl
				]))->driver($defaultLoginChannel['default_login_channel'])->stateless()->redirect()->getTargetUrl();
			}
		}
	}


}