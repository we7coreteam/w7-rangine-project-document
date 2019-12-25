<?php

namespace W7\App\Handler\Exception;

use Psr\Http\Message\ResponseInterface;
use W7\App;
use W7\Core\Exception\ResponseExceptionAbstract;
use W7\Core\Exception\RouteNotAllowException;
use W7\Core\Exception\RouteNotFoundException;
use W7\Core\Exception\ValidatorException;
use W7\Core\Session\Session;
use W7\Core\View\View;
use W7\Core\Exception\Handler\ExceptionHandler as ExceptionHandlerAbstract;

class ExceptionHandler extends ExceptionHandlerAbstract {
	public function handle(ResponseExceptionAbstract $e) : ResponseInterface {
		if ($e instanceof RouteNotFoundException || $e instanceof RouteNotAllowException) {
			$route = icontext()->getRequest()->getUri()->getPath();
			//如果访问的是admin下的路由，先检测是否登录
			if (substr($route, 0, 6) == '/admin') {
				$session = new Session();
				$session->start(icontext()->getRequest());
				if (!$session->get('user')) {
					return icontext()->getResponse()->redirect('/login');
				}
			}
			//如果是访问预览的连接，判断该文档是否需要登录后预览
			if (substr($route, 0, 8) === '/chapter') {
				$session = new Session();
				$session->start(icontext()->getRequest());
				if (!$session->get('user')) {
					$documentId = explode('/', $route)[2] ?? '';
					$documentId = explode('?', $documentId)[0];
					$document = App\Model\Logic\DocumentLogic::instance()->getById($documentId);
					if ($document && $document->is_public == App\Model\Entity\Document::LOGIN_PREVIEW_DOCUMENT) {
						return icontext()->getResponse()->redirect('/login?redirect=' . ienv('API_HOST') . ltrim($route, '/'));
					}
				}
			}
			return App::getApp()->getContext()->getResponse()->html(iloader()->singleton(View::class)->render('@public/index'));
		}

		if ($e instanceof ValidatorException) {
			return (new App\Exception\ErrorHttpException($e->getMessage(), [], $e->getCode()))->render();
		}
		return parent::handle($e);
	}
}