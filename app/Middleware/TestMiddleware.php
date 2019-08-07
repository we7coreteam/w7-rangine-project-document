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

class TestMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		//这里是中间件一些代码
		if (false) {
			return App::getApp()->getContext()->getResponse()->json(
				['message' => '权限不足', 'data' => null, 'status' => false]
			);
		}

		return $handler->handle($request);
	}
}
