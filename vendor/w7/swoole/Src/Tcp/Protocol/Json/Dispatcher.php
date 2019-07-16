<?php

namespace W7\Tcp\Protocol\Json;

use Swoole\Server;
use W7\Http\Message\Server\Request;
use W7\Http\Message\Server\Response;
use W7\Tcp\Protocol\DispatcherInterface;
use W7\Tcp\Server\Dispather;

class Dispatcher implements DispatcherInterface {
	public function dispatch(Server $server, $fd, $data) {
		$params = json_decode($data, true);
		$params['url'] = $params['url'] ?? '';
		$params['data'] = $params['data'] ?? [];

		$psr7Request = new Request('POST', $params['url'], [], null);
		$psr7Request = $psr7Request->withParsedBody($params['data']);
		$psr7Response = new Response();

		$dispather = \iloader()->singleton(Dispather::class);
		$psr7Response = $dispather->dispatch($psr7Request, $psr7Response);

		$content = $psr7Response->getBody()->getContents();
		$server->send($fd, $content);
	}
}