<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class DocumentPermissionMiddleware extends MiddlewareAbstract {
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		$documentId = $request->post('document_id');
		if (!$documentId) {
			throw new \RuntimeException('Invalid document_id');
		}

		$user = $request->getAttribute('user');
		$documentPermission = (new DocumentPermissionLogic())->get($documentId, $user->id);
		if ($documentPermission) {
			$request = $request->withAttribute('permission', $documentPermission);
		}

		return parent::process($request, $handler);
	}
}