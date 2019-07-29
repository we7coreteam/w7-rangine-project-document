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

class EventMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		App\Event\ChangeDocumentEvent::instance()->addSubscriber(App\Subscriber\ClearDocumentCacheSubscriber::class);
		App\Event\ChangeAuthEvent::instance()->addSubscriber(App\Subscriber\ClearAuthCacheSubscriber::class);
		App\Event\ChangeChapterEvent::instance()->addSubscriber(App\Subscriber\ClearChapterCacheSubscriber::class);
		App\Event\CreateDocumentEvent::instance()->addSubscribers([
			App\Subscriber\ClearAuthCacheSubscriber::class,
			//App\Subscriber\ClearCategoryCacheSubscriber::class
		]);
		return $handler->handle($request);
	}
}
