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

use W7\App\Model\Entity\Document\History\HistoryChapter;
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class HistoryChapterLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getCatalog($history_id, $document_id)
	{
		$list = HistoryChapter::query()
			->select('id', 'chapter_id', 'name', 'sort', 'parent_id', 'is_dir', 'default_show_chapter_id')
			->where('history_id', $history_id)
			->where('document_id', $document_id)
			->orderBy('parent_id', 'asc')
			->orderBy('sort', 'asc')->get()->toArray();

		if (empty($list)) {
			return [];
		}

		$result = [];
		foreach ($list as $id => $item) {
			$item['is_dir'] = $item['is_dir'] == Chapter::IS_DIR ? true : false;
			$result[$item['chapter_id']] = $item;
			$result[$item['chapter_id']]['children'] = [];
		}
		return $this->getTree($result, 0);
	}

	private function getTree($data, $pid = 0, $i = 0)
	{
		$tree = [];
		++$i;
		foreach ($data as $k => $v) {
			$v['level'] = $i;
			if ($v['parent_id'] == $pid) {
				$v['children'] = $this->getTree($data, $v['chapter_id'], $i);
				$tree[] = $v;
				unset($data[$k]);
			}
		}
		return $tree;
	}
}
