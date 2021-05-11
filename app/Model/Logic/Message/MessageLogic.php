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

namespace W7\App\Model\Logic\Message;

use W7\App\Model\Entity\Message;
use W7\App\Model\Logic\BaseLogic;

class MessageLogic extends BaseLogic
{
	/**
	 * @var Message\Message
	 */
	protected $model = Message\Message::class;

	/**
	 * 用户信息/公告
	 * @param $userId
	 * @param int $page
	 * @param int $limit
	 * @param string $targetType
	 * @param null $isRead
	 * @param array $createdAt
	 * @return mixed
	 */
	public function getFrontendLists($userId, $page = 1, $limit = 20, $targetType = '', $isRead = null, $createdAt = [], $type = '')
	{
		if ($type) {
		} else {
			$type = '';
			if ($targetType) {
				$tmpTargetType = explode('_', $targetType);
				$type = $tmpTargetType[0];
			}
		}

		switch ($type) {
			case Message\Message::TYPE_REMIND: // 通知
				$query = $this->model::query()
					->with('text')
					->where('to_id', $userId);
				if ($targetType) {
					$query->where('target_type', $targetType);
				}
				if ($createdAt) {
					$query->whereBetween('message.created_at', $createdAt);
				}
				if ($isRead != null) {
					$query->where('is_read', $isRead);
				}
				$query->orderBy('id', 'desc');
				break;
			default: // 通知 + 公告
				$query = $this->model::query()
					->with('text')
					->where('to_id', $userId);
				if ($targetType) {
					$query->where('target_type', $targetType);
				}
				if ($createdAt) {
					$query->whereBetween('message.created_at', $createdAt);
				}
				if ($isRead != null) {
					$query->where('is_read', $isRead);
				}
				$query->orderBy('id', 'desc');
				break;
		}

		return $query->paginate($limit, ['*'], '', $page)->toArray();
	}

	public function updateIsReadAll($userId)
	{
		// 系统通知
		$this->model::query()->where('is_read', Message\Message::IS_READ_N)
			->where('to_id', $userId)->whereIn('type', [Message\Message::TYPE_REMIND])->update(['is_read' => Message\Message::IS_READ_Y]);
	}

	public function updateIsRead($userId, array $ids)
	{
		// 系统通知
		$this->model::query()->whereIn('id', $ids)->where('to_id', $userId)
			->whereIn('type', [Message\Message::TYPE_REMIND])->update(['is_read' => Message\Message::IS_READ_Y]);
		return true;
	}

	public function deleteSelection($userId, array $ids)
	{
		// 系统通知
		$this->model::query()->whereIn('id', $ids)->where('to_id', $userId)
			->whereIn('type', [Message\Message::TYPE_REMIND])->delete();
		return true;
	}
}
