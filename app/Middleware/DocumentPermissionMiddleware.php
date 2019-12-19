<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Model\Entity\DocumentPermission;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class DocumentPermissionMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$documentId = $request->post('document_id');
		if (!$documentId) {
			return parent::process($request, $handler);
		}

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if ($user->isFounder) {
			$user->isManager = true;
			$user->isOperator = true;
			$user->isReader = true;
		} else {
			/**
			 * @var DocumentPermission $documentPermission
			 */
			$documentPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($documentId, $user->id);
			if ($documentPermission) {
				$user->isManager = $documentPermission->isManager;
				$user->isOperator = $documentPermission->isOperator;
				$user->isReader = $documentPermission->isReader;
			} else {
				$user->isManager = false;
				$user->isOperator = false;
				$user->isReader = false;
			}
		}
		$request = $request->withAttribute('user', $user);

		return parent::process($request, $handler);
	}
}