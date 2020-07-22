<?php

/**
 * WeEngine Team
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
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App;

class CorsApiMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$headerHost = $request->getHeader('origin');
		if (!$headerHost) {
			$headerHost = $request->getHeader('referer');
		}
		$headerHost = current($headerHost);
		$urlInfo = parse_url($headerHost);
		$headerHost = ($urlInfo['scheme'] ?? '') . '://' . ($urlInfo['host'] ?? '');

		$response = App::getApp()->getContext()->getResponse();

		$header=$request->getHeaders();
		$allowHeaders=[];
		foreach ($header as $key =>$val){
			$allowHeaders[count($allowHeaders)]=$key;
		}

		$response = $response->withHeader('Access-Control-Allow-Origin', $headerHost);
		$response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
		$response = $response->withHeader('Access-Control-Allow-Headers', implode(',', $this->allowHeaders));
		$response = $response->withHeader('Access-Control-Allow-Methods', 'POST, GET, DELETE, PUT, PATCH, OPTIONS');
		if ($request->getMethod() == 'OPTIONS') {
			return $response->json('success');
		}

		App::getApp()->getContext()->setResponse($response);
		return $handler->handle($request);
	}
}
