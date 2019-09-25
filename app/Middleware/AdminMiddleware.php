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
use W7\App;
use W7\Core\Middleware\MiddlewareAbstract;

class AdminMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$user_id = $request->session->get('user_id');
		if (!$user_id) {
			return App::getApp()->getContext()->getResponse()->json(['message' => '用户未登录', 'data' => null, 'status' => false, 'code' => 444]);
		}
		$request->document_user_id = $user_id;
		$logic = new App\Model\Logic\UserAuthorizationLogic();
		$request->document_user_auth = $logic->getUserAuthorizations($user_id);
		App::getApp()->getContext()->setRequest($request);
		return $handler->handle($request);
	}
}
