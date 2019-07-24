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
		//这里是中间件一些代码 C6F3U6FDgQLBFRRbAAo0o0o CdF3UdFDg8KBFVXB24A wfy(C9F8QdEBAUMBFJXB24D)
		//        $key =App\Model\Service\EncryptorLogic::encrypt('1503343424_1');
		//        $cache->set($key,1);
		//        return App::getApp()->getContext()->getResponse()->json($key);
		$token = $request->getHeaderLine('document_access_token');
		!$token && $token = $request->input('document_access_token');
		if (!$token) {
			return App::getApp()->getContext()->getResponse()->json(['message' => '缺少用户票据', 'data' => null, 'status' => false, 'code' => 444]);
		}
		$access_token = icache()->get($token);
		if (!$access_token) {
			return App::getApp()->getContext()->getResponse()->json(['message' => '错误的票据', 'data' => null, 'status' => false, 'code' => 444]);
		}
		$request->document_user_id = $access_token;
		$logic = new App\Model\Logic\UserAuthorizationLogic();
		$request->document_user_auth = $logic->getUserAuthorizations($access_token);

		return $handler->handle($request);
	}
}
