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

namespace W7\App\Model\Logic\Document;

use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ChapterContentLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getLayoutLabel()
	{
		return [
			ChapterContent::LAYOUT_MARKDOWM => 'MARKDOWN',
			ChapterContent::LAYOUT_HTTP => 'HTTP'
		];
	}
}
