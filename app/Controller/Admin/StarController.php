<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\Star;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class StarController extends BaseController
{
	public function add(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传',
		]);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isReader) {
			throw new ErrorHttpException('无权限操作该文档');
		}

		$document = DocumentLogic::instance()->getById($params['document_id']);
		if (empty($document)) {
			throw new ErrorHttpException('您操作的文档不存在');
		}

		$star = new Star();
		$star->user_id = $user->id;
		$star->document_id = $params['document_id'];
		$star->save();

		return $this->data('success');
	}

	public function delete(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传',
		]);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isReader) {
			throw new ErrorHttpException('无权限操作该文档');
		}

		$document = DocumentLogic::instance()->getById($params['document_id']);
		if (empty($document)) {
			throw new ErrorHttpException('您操作的文档不存在');
		}

		Star::query()->where('document_id', '=', $params['document_id'])->where('user_id', '=', $user->id)->delete();
		return $this->data('success');
	}
}