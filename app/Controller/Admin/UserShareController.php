<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\Share;
use W7\App\Model\Logic\UserShareLogic;
use W7\Http\Message\Server\Request;

class UserShareController extends BaseController
{
	public function all(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '请指定文档',
		]);

		$page = intval($request->post('page'));

		$user = $request->getAttribute('user');
		$query = Share::query()->where('sharer_id', '=', $user->id)->where('document_id', '=', $params['document_id'])->orderByDesc('id');
		$list = $query->paginate(null, '*', 'page', $page);

		$result['data'] = [];
		foreach ($list->items() as $row) {
			$result['data'][] = [
				'sharer_name' => $row->sharer->username,
				'share_url' => UserShareLogic::instance()->getShareUrl($row->sharer_id, $row->document_id, $row->chapter_id),
				'user_name' => $row->user->username,
				'time' => $row->created_at->toDateTimeString()
			];
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function shareUrl(Request $request)
	{
		$params = $this->validate($request, [
			'chapter_id' => 'required|integer',
			'document_id' => 'required|integer'
		]);

		$user = $request->getAttribute('user');

		return $this->data(UserShareLogic::instance()->getShareUrl($user->id, $params['document_id'], $params['chapter_id']));
	}
}