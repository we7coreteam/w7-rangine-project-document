<?php

namespace W7\Tcp\Protocol\Thrift\Core;

use W7\Http\Message\Server\Request;
use W7\Http\Message\Server\Response;
use W7\Tcp\Server\Dispather;

class DispatcherHandle implements DispatcherIf {
	public function run($params) {
		$params = json_decode($params, true);
		$params['url'] = $params['url'] ?? '';
		$params['data'] = $params['data'] ?? [];

		$psr7Request = new Request('POST', $params['url'], [], null);
		$psr7Request = $psr7Request->withParsedBody($params['data']);
		$psr7Response = new Response();

		$dispather = \iloader()->singleton(Dispather::class);
		$psr7Response = $dispather->dispatch($psr7Request, $psr7Response);

		return $psr7Response->getBody()->getContents();
	}
}