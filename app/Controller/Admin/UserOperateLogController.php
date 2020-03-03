<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\Star;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserOperateLog;
use W7\Http\Message\Server\Request;

class UserOperateLogController extends BaseController
{
	public function all(Request $request)
	{
		$name = $request->post('name');
		$page = intval($request->post('page'));
		//时间按天为单位
		$time = intval($request->post('time'));
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$query = UserOperateLog::query()->where('user_id', '=', $user->id)->where('operate', '=', UserOperateLog::PREVIEW)->groupBy(['document_id'])->orderByDesc('created_at');
		if ($name) {
			$query->whereHas('document', function ($query) use ($name) {
				return $query->where('name', 'LIKE', "%{$name}%");
			});
		}
		if ($time) {
			$query = $query->where('created_at', '<', time() - 86400 * $time);
		}

		$list = $query->paginate(null, ['user_id', 'document_id', 'operate', 'remark', 'created_at'], 'page', $page);

		$document = $list->items();
		if (!empty($document)) {
			foreach ($document as $i => $row) {
				$star = Star::query()->where('user_id', '=', $user->id)->where('document_id', '=', $row->document_id)->first();
				$result['data'][] = [
					'id' => $row->document->id,
					'name' => $row->document->name,
					'has_star' => $star ? true : false,
					'author' => [
						'name' => $row->document->user->username
					],
					'description' => $row->document->descriptionShort,
					'is_public' => $row->document->isPublicDoc,
					'time' => $row->created_at->toDateTimeString()
				];
			}
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function deleteByDocumentId(Request $request)
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
		UserOperateLog::query()->where('document_id', '=', $params['document_id'])->where('user_id', '=', $user->id)->delete();

		return $this->data('success');
	}
}