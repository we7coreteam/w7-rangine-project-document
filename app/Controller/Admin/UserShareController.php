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
		$page = intval($request->post('page'));

		$user = $request->getAttribute('user');
		$query = Share::query()->where('sharer_id', '=', $user->id)->orderByDesc('id');
		$list = $query->paginate(null, '*', 'page', $page);

		$result['data'] = [];
		foreach ($list->items() as $row) {
			$result['data'][] = [
				'sharer_name' => $row->sharer->username,
				'share_key' => UserShareLogic::instance()->makeShareKeyByUidAndChapterId($row->sharer_id, $row->chapter_id),
				'user_name' => $row->user->username,
				'time' => $row->created_at->toDateTimeString()
			];
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function shareKey(Request $request)
	{
		$params = $this->validate($request, [
			'chapter_id' => 'required'
		]);

		$user = $request->getAttribute('user');

		return $this->data(UserShareLogic::instance()->makeShareKeyByUidAndChapterId($user->id, $params['chapter_id']));
	}
}