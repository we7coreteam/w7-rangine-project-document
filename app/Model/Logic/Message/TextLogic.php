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

class TextLogic extends BaseLogic
{
	protected $model = Message\Text::class;
	protected $noAllowActions = [];

	public function add($content, $title = '')
	{
		return Message\Text::query()->create([
			'title' => $title,
			'content' => $content
		]);
	}
}
