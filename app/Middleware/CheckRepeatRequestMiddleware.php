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

class CheckRepeatRequestMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if (icache()->channel('db')->get('repeat_'.$request->document_user_id)) {
			return App::getApp()->getContext()->getResponse()->json(['message' => '重复请求，请稍后再试', 'data' => null, 'status' => false, 'code' => 444]);
		}
		icache()->channel('db')->set('repeat_'.$request->document_user_id, 1, 2);
		return $handler->handle($request);
	}
}
