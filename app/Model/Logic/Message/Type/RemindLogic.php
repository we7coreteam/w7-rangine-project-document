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

namespace W7\App\Model\Logic\Message\Type;

use W7\App\Model\Entity\Message;
use W7\App\Model\Logic\Message\TextLogic;
use W7\App\Model\Logic\Message\MessageLogic;

class RemindLogic extends MessageLogic
{
	public function add($systemRemindUid, $toId, $content, $targetType, $targetId = 0)
	{
		// 用户订阅的消息

		$textLogic = new TextLogic();
		$text = $textLogic->add($content);

		$data = [
			'from_id' => $systemRemindUid,
			'to_id' => $toId,
			'text_id' => $text->id,
			'type' => Message\Message::TYPE_REMIND,
			'target_type' => $targetType,
			'target_id' => $targetId,
			'target_url' => '',
		];
		return Message\Message::query()->create($data);
	}
}
