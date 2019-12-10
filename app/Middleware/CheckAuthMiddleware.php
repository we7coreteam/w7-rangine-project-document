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

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\UserLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class CheckAuthMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$user = $request->session->get('user');
		if (empty($user)) {
			throw new ErrorHttpException('请先登录', [], 444);
		}
		$request = $request->withAttribute('user', UserLogic::instance()->getByUid($user['uid']));
		return parent::process($request, $handler);
	}
}
