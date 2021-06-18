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

namespace W7\App\Model\Entity\Document\History;

use W7\App\Model\Entity\BaseModel;
use W7\App\Model\Entity\Document\ChapterContent;

class HistoryChapter extends BaseModel
{
	protected $table = 'document_history_chapter';

	public function content()
	{
		return $this->hasOne(ChapterContent::class, 'chapter_id', 'chapter_id');
	}
}
