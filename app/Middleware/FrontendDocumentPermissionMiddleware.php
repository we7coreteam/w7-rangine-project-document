<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\DocumentLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class FrontendDocumentPermissionMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$documentId = $request->input('document_id');
		if (!$documentId) {
			return parent::process($request, $handler);
		}

		$document = DocumentLogic::instance()->getById($documentId);
		if ($document && !$document->isPublicDoc && !$request->session->get('user')) {
			throw new ErrorHttpException('请先登录', [], 444);
		}

		return parent::process($request, $handler);
	}
}