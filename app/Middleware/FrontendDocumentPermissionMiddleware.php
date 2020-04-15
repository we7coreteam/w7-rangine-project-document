<?php

namespace W7\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\App\Model\Logic\UserLogic;
use W7\Core\Middleware\MiddlewareAbstract;

class FrontendDocumentPermissionMiddleware extends MiddlewareAbstract
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$user = $request->session->get('user');
		if (empty($user)) {
			$user = new User();
		} else {
			/**
			 * @var User $user
			 */
			$user = UserLogic::instance()->getByUid($user['uid']);
		}

		$documentId = $request->input('document_id');
		if (!$documentId) {
			return parent::process($request, $handler);
		}
		$document = DocumentLogic::instance()->getById($documentId);
		if (!$document) {
			$request = $request->withAttribute('user', $user);
			return parent::process($request, $handler);
		}

		$user->isReader = $user->isFounder;
		if ($document->isPublicDoc) {
			$user->isReader = true;
		} elseif ($document->isPrivateDoc && !empty($user->id)) {
			$documentPermission = DocumentPermissionLogic::instance()->getByDocIdAndUid($documentId, $user->id);
			if ($documentPermission) {
				$user->isReader = $documentPermission->isReader;
			}
		} elseif ($document->isLoginPreviewDoc) {
			$user->isReader = empty($user->id) ? false : true;
		}

		$request = $request->withAttribute('user', $user);
		return parent::process($request, $handler);
	}
}