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

use W7\App\Model\Entity\Document;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class HistoryLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = Document\History::class;

	public function createHistory($document_id, $creator_id)
	{
		$document = Document::query()->where('document.id', $document_id)->with(['chapter', 'chapter.content'])->first()->toArray();
		if ($document->is_history != Document::IS_HISTORY_YES) {
			return true;
		}
		$historyData = [
			'document_id' => $document['id'],
			'name' => $document['name'],
			'creator_id' => $creator_id,
		];
		$history = Document\History::query()->create($historyData);
		foreach ($document['chapter'] as $chapter) {
			$historyChapterData = [
				'chapter_id' => $chapter['id'],
				'parent_id' => $chapter['parent_id'],
				'name' => $chapter['name'],
				'history_id' => $history->id,
				'sort' => $chapter['sort'],
				'is_dir' => $chapter['is_dir'],
				'levels' => $chapter['levels'],
				'default_show_chapter_id' => $chapter['default_show_chapter_id'],
			];
			$historyChapter = Document\History\HistoryChapter::query()->create($historyChapterData);
			if ($chapter['content']) {
				$historyChapterContentData = [
					'history_chapter_id' => $historyChapter->id,
					'content' => $chapter['content']['content'],
					'layout' => $chapter['content']['layout']
				];
				Document\History\HistoryChapterContent::query()->create($historyChapterContentData);
			}
		}

		return $history->id;
	}

	public function getById($history_id, $document_id)
	{
		return Document\History::query()->where([
			['id', '=', $history_id],
			['document_id', '=', $document_id]
		])->first();
	}
}
