<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Exception\ErrorHttpException;
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
			throw new ErrorHttpException('Invalid document_id');
		}

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			/**
			 * @var DocumentPermission $documentPermission
			 */
			$documentPermission = (new DocumentPermissionLogic())->getByDocIdAndUid($documentId, $user->id);
			$user->isManage = $documentPermission->isManager();
			$user->isOperator = $documentPermission->isOperator();
			$user->isReader = $documentPermission->isReader();
		} else {
			$user->isManage = true;
			$user->isOperator = true;
			$user->isReader = true;
		}

		$request = $request->withAttribute('user', $user);

		return parent::process($request, $handler);
	}
}