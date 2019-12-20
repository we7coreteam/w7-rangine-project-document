<?php

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Document\ChapterOperateLog;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ChapterOperateLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getByChapterAndOperate($chapterId, $operate)
	{
		return ChapterOperateLog::query()->where('chapter_id', '=', $chapterId)->where('operate', '=', $operate)->first();
	}
}