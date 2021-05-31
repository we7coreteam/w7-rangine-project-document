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

use W7\App\Model\Entity\UserStatus;
use W7\Core\Helper\Traiter\InstanceTraiter;
use W7\App\Model\Entity\User;

class UserStatusLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = UserStatus::class;

	public function getStatus($user_id, $page = 1, $limit = 20)
	{
		return UserStatus::query()
			->where([
				['is_show', '=', 1],
				['operator_id', '=', $user_id],
			])
			->orderBy('created_at', 'desc')
			->paginate($limit, ['*'], 'page', $page);
	}

	public function createStatus($row, User $operator, $operate)
	{
		$data = [];
		switch ($operate) {
			case UserStatus::CREATE_DOCUMENT:
				$data = [
					'user_id' => $operator->id,
					'operator_id' => $operator->id,
					'type' => UserStatus::CREATE_DOCUMENT,
					'relation' => class_basename($row),
					'relation_id' => $row->id,
					'is_show' => $row->is_public == 2 ? 0 : 1,
					'remark' => $operator->username . '创建了文档' . $row->name
				];
				break;
			case UserStatus::COLLECT_DOCUMENT:
				$data = [
					'user_id' => $operator->id,
					'operator_id' => $operator->id,
					'type' => UserStatus::COLLECT_DOCUMENT,
					'relation' => class_basename($row),
					'relation_id' => $row->id,
					'remark' => $operator->username . '收藏了文档' . $row->name
				];
				break;
			case UserStatus::CREATE_COLUMN:
				$data = [
					'user_id' => $operator->id,
					'operator_id' => $operator->id,
					'type' => UserStatus::CREATE_COLUMN,
					'relation' => class_basename($row),
					'relation_id' => $row->id,
					'remark' => $operator->username . '创建了专栏' . $row->name
				];
				break;
			case UserStatus::SUB_COLUMN:
				$data = [
					'user_id' => $operator->id,
					'operator_id' => $operator->id,
					'type' => UserStatus::SUB_COLUMN,
					'relation' => class_basename($row),
					'relation_id' => $row->id,
					'remark' => $operator->username . '订阅了专栏' . $row->name
				];
				break;
			case UserStatus::FOLLOW_USER:
				$data = [
					'user_id' => $operator->id,
					'operator_id' => $operator->id,
					'type' => UserStatus::FOLLOW_USER,
					'relation' => class_basename($row),
					'relation_id' => $row->id,
					'remark' => $operator->username . '关注了用户' . $row->username
				];
				break;
			case UserStatus::CREATE_ARTICLE:
				$data = [
					'user_id' => $operator->id,
					'operator_id' => $operator->id,
					'type' => UserStatus::CREATE_ARTICLE,
					'relation' => class_basename($row),
					'relation_id' => $row->id,
					'is_show' => 0,
					'remark' => $operator->username . '创建了文章' . $row->title
				];
				break;
		}
		if ($data) {
			return parent::store($data);
		}
		return;
	}

	public function deleteStatus($row, $operator_id, $operate)
	{
		$where = [];
		switch ($operate) {
			case UserStatus::CREATE_DOCUMENT:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::CREATE_DOCUMENT],
					['relation_id', '=', $row->id]
				];
				break;
			case UserStatus::COLLECT_DOCUMENT:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::COLLECT_DOCUMENT],
					['relation_id', '=', $row->id]
				];
				break;
			case UserStatus::SUB_COLUMN:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::SUB_COLUMN],
					['relation_id', '=', $row->id]
				];
				break;
			case UserStatus::FOLLOW_USER:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::FOLLOW_USER],
					['relation_id', '=', $row->id]
				];
				break;
			case UserStatus::CREATE_ARTICLE:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::CREATE_ARTICLE],
					['relation_id', '=', $row->id]
				];
				break;
		}
		if ($where) {
			return UserStatus::where($where)->delete();
		}
		return;
	}

	public function changeShow($row, $operator_id, $operate, $is_show = UserStatus::SHOW)
	{
		$where = [];
		switch ($operate) {
			case UserStatus::CREATE_DOCUMENT:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::CREATE_DOCUMENT],
					['relation_id', '=', $row->id]
				];
				break;
			case UserStatus::CREATE_ARTICLE:
				$where = [
					['operator_id', '=', $operator_id],
					['type', '=', UserStatus::CREATE_ARTICLE],
					['relation_id', '=', $row->id]
				];
				break;
		}
		if ($where) {
			return UserStatus::where($where)->update(['is_show' => $is_show]);
		}
		return;
	}
}
