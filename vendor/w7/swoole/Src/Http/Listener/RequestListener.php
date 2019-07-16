<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:08
 */

namespace W7\Http\Listener;

use W7\App;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use W7\Core\Config\Event;
use W7\Core\Listener\ListenerAbstract;
use W7\Http\Message\Server\Request as Psr7Request;
use W7\Http\Message\Server\Response as Psr7Response;
use W7\Http\Server\Dispather;

class RequestListener extends ListenerAbstract {
	public function run(...$params) {
		list($server, $request, $response) = $params;
		return $this->dispatch($server, $request, $response);
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @return \Psr\Http\Message\ResponseInterface|Response
	 * @throws \ReflectionException
	 */
	private function dispatch(Server $server, Request $request, Response $response) {
		ievent(Event::ON_USER_BEFORE_REQUEST);

		$context = App::getApp()->getContext();
		$context->setContextDataByKey('workid', $server->worker_id);
		$context->setContextDataByKey('coid', Coroutine::getuid());

		$psr7Request = Psr7Request::loadFromSwooleRequest($request);
		$psr7Response = Psr7Response::loadFromSwooleResponse($response);

		$dispather = \iloader()->singleton(Dispather::class);
		$psr7Response = $dispather->dispatch($psr7Request, $psr7Response);
		$psr7Response->send();

		ievent(Event::ON_USER_AFTER_REQUEST);

		$context->destroy();
	}
}
