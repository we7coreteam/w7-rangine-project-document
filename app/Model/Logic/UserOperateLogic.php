<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\UserOperateLog;
use W7\Core\Helper\Traiter\InstanceTraiter;

class UserOperateLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = UserOperateLog::class;

	public function getByChapterAndOperate($chapterId, $operate)
	{
		return UserOperateLog::query()->where('chapter_id', '=', $chapterId)->where('operate', '=', $operate)->first();
	}

	public function clearByUid($userId)
	{
		return UserOperateLog::query()->where('user_id', '=', $userId)->delete();
	}

	public function clearByDocId($documentId)
	{
		return UserOperateLog::query()->where('document_id', '=', $documentId)->delete();
	}

	public function lists($where = [], $page = 1, $size = 20, $hasCreateChapter = false)
	{
		$query = UserOperateLog::query()
			->select('user_operate_log.*')
			->with(['user','document','column','document.user' => function ($query) {
				$query->select(['id', 'username', 'avatar']);
			}]);
		//只展示公开的
		$query->leftJoin('document', 'document.id', 'user_operate_log.document_id')->where('document.is_public', Document::PUBLIC_DOCUMENT);
		if (!empty($where['user_id'])) {
			$query->where('user_operate_log.user_id', $where['user_id']);
		}
		if (!empty($where['operate'])) {
			(!$hasCreateChapter && in_array(UserOperateLog::CREATE, $where['operate']))
				? $query->where(function ($query) use ($where) {
					$query->where(function ($query) {
						$query->where('user_operate_log.operate', UserOperateLog::CREATE)->where('chapter_id', 0);
					})->orWhere(function ($query) use ($where) {
						$query->whereIn('user_operate_log.operate', array_diff($where['operate'], [UserOperateLog::CREATE]));
					});
				})
				: $query->whereIn('user_operate_log.operate', $where['operate']);
		}

		return $query->orderBy('user_operate_log.id', 'desc')->paginate($size, ['*'], '', $page);
	}

	public function createOperateLog($row, $operate)
	{
		$data = [];
		switch ($operate) {
			case UserOperateLog::COLUMN_CREATE:
				$data = [
					'user_id' => $row->user_id,
					'column_id' => $row->id,
					'operate' => UserOperateLog::COLUMN_CREATE,
					'remark' => $row->user->username . '创建专栏' . $row->name
				];
				break;
			case UserOperateLog::COLUMN_SUB:
				$data = [
					'user_id' => $row->user_id,
					'column_id' => $row->column_id,
					'operate' => UserOperateLog::COLUMN_SUB,
					'remark' => $row->user->username . '订阅专栏' . $row->column->name
				];
				break;
			case UserOperateLog::COLUMN_UNSUB:
				$data = [
					'user_id' => $row->user_id,
					'column_id' => $row->column_id,
					'operate' => UserOperateLog::COLUMN_UNSUB,
					'remark' => $row->user->username . '取消订阅专栏' . $row->column->name
				];
				break;
		}
		return parent::store($data);
	}
}
