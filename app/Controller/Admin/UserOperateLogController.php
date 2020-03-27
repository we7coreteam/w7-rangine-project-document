<?php

namespace W7\App\Controller\Admin;

use Illuminate\Support\Facades\DB;
use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Star;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserOperateLog;
use W7\Http\Message\Server\Request;

class UserOperateLogController extends BaseController
{
	/**
	 * 目前获取的是用户阅读过的文档数据
	 * @param Request $request
	 * @return array
	 */
	public function getUserReaderLog(Request $request)
	{
		$name = $request->post('name');
		$page = intval($request->post('page', 1));
		$pageSize = intval($request->post('page_size', 15));
		//时间按天为单位
		$time = intval($request->post('time'));

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');

		$query = UserOperateLog::query();
		if ($name) {
			$query->whereHas('document', function ($query) use ($name) {
				return $query->where('name', 'LIKE', "%{$name}%");
			});
		}
		if ($time) {
			$query = $query->where('created_at', '<', time() - 86400 * $time);
		}
		$groupOperate = UserOperateLog::query()->where('user_id', '=', $user->id)
			->where('operate', '=', UserOperateLog::PREVIEW)
			->groupBy(['document_id'])->orderByDesc('max_id')->select([
				'document_id',
				DB::raw('max(id) max_id')
			])->take($pageSize)->skip(($page - 1) * $pageSize)->getQuery();
		$dataQuery = clone $query;
		$dataQuery = $dataQuery->joinSub($groupOperate, 'sub', 'id', '=', 'max_id')->orderByDesc('id');

		foreach ($dataQuery->get() as $i => $row) {
			$star = Star::query()->where('user_id', '=', $user->id)->where('document_id', '=', $row->document_id)->where('chapter_id', '=', 0)->first();
			$result['data'][] = [
				'id' => $row->id,
				'document_id' => $row->document->id,
				'name' => $row->document->name,
				'star_id' => !empty($star) ? $star->id : '',
				'author' => [
					'name' => $row->document->user->username
				],
				'description' => $row->document->descriptionShort,
				'is_public' => $row->document->isPublicDoc,
				'time' => $row->created_at->toDateTimeString()
			];
		}

		$query = $query->where('user_id', '=', $user->id)->where('operate', '=', UserOperateLog::PREVIEW)->groupBy(['document_id']);
		$list = $query->paginate($pageSize, ['*'], 'page', $page);
		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	/**
	 * 获取文档的所有操作记录
	 * @param Request $request
	 * @return array
	 */
	public function getByDocument(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer'
		]);
		$page = intval($request->post('page'));
		$pageSize = intval($request->post('page_size'));
		//时间按天为单位
		$time = intval($request->post('time'));
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$query = UserOperateLog::query()->where('document_id', '=', $params['document_id'])
			->where('operate', '!=', UserOperateLog::PREVIEW)->where('remark', '!=' , '')->orderByDesc('created_at');
		if ($time) {
			$query = $query->where('created_at', '<', time() - 86400 * $time);
		}
		$list = $query->paginate($pageSize, ['id', 'user_id', 'document_id', 'operate', 'remark', 'created_at'], 'page', $page);
		foreach ($list->items() as $i => $row) {
			$result['data'][] = [
				'id' => $row->id,
				'remark' => $row->remark,
				'time' => $row->created_at->toDateTimeString()
			];
		}

		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $this->data($result);
	}

	public function deleteById(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer',
		], [
			'document_id.required' => '文档ID必传'
		]);
		$operateLogId = $request->post('operate_log_id');
		$operateLogType = $request->post('operate_log_type', UserOperateLog::PREVIEW);

		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		$query = UserOperateLog::query()->where('document_id', '=', $params['document_id']);
		if ($operateLogId) {
			$query = $query->where('id', '=', $operateLogId);
		} else {
			$query = $query->where('operate', '=', $operateLogType);
		}
		if (!$user->isManager) {
			$query = $query->where('user_id', '=', $user->id);
		}
		$query->delete();

		return $this->data('success');
	}
}