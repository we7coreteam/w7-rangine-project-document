<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\AppLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class AppAuthMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$params = $request->getParsedBody();
		if ((empty($params['appid']) && empty($params['sign'])) || !empty($request->session->get('user'))) {
			return $handler->handle($request);
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

		if (empty($app->user_id)) {
			$user = [
				'username' => $app->name . $app->appid,
				'userpass' => trim($app->appid),
			];
			$user['remark'] = $app->name;
			try {
				$app->user_id = UserLogic::instance()->createUser($user);
			} catch (Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
			$app->save();
		}

		$user = UserLogic::instance()->getByUid($app->user_id);
		$request->session->destroy();
		$request->session->set('user', [
			'uid' => $user->id,
			'username' => $user->username,
		]);

		return $handler->handle($request);
	}
}
