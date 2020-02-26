<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\AppLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class AppCheckMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$params = $request->getParsedBody();
		if (empty($params['appid']) || empty($params['sign'])) {
			throw new ErrorHttpException('Invalid appid or sign');
		}

		/**
		 * @var AppLogic $appLogic
		 */
		$appLogic = iloader()->singleton(AppLogic::class);
		$app = $appLogic->getByAppId($params['appid']);

		if (empty($app)) {
			throw new ErrorHttpException('Invalid appid or sign');
		}

		$sign = $appLogic->getSign($params, $app->appsecret);

		if ($sign !== $params['sign']) {
			throw new ErrorHttpException('Invalid sign');
		}

		$request = $request->withAttribute('app', $app);
		return $handler->handle($request);
	}
}
