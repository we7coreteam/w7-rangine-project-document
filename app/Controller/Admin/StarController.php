<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Star;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class StarController extends BaseController
{
	public function all(Request $request)
	{
		$name = $request->post('name');
		$page = intval($request->post('page'));
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$query = Star::query()->where('user_id', '=', $user->id);
		if ($name) {
			$query->whereHas('document', function ($query) use ($name) {
				return $query->where('name', 'LIKE', "%{$name}%");
			});
		}
		$list = $query->paginate(null, '*', 'page', $page);
		$data = [];
		foreach ($list->items() as $row) {
			if ($row->chapter_id) {
				$name = $row->chapter->name;
			} else {
				$name = $row->document->name;
			}
			$data[] = [
				'id' => $row->id,
				'name' => $name,
				'author' => [
					'name' => $row->document->user->username
				],
				'is_public' => $row->document->isPublicDoc,
				'document_id' => $row->document->id,
				'chapter_id' => $row->document->id
			];
		}

		$result['data'] = $data;
		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

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
		$star->chapter_id = (int)$request->post('chapter_id', 0);
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
		$chapterId = (int)$request->post('chapter_id', 0);

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

		Star::query()->where('document_id', '=', $params['document_id'])->where('user_id', '=', $user->id)->where('chapter_id', '=', $chapterId)->delete();
		return $this->data('success');
	}
}