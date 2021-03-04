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
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiExtend;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserOperateLog;
use W7\Core\Helper\Traiter\InstanceTraiter;
use W7\App\Model\Service\CdnLogic;

class ChapterLogic extends BaseLogic
{
	use InstanceTraiter;

	/**
	 * 获取章节目录
	 * @param $id
	 * @return array
	 */
	public function getCatalog($documentId)
	{
		$list = Chapter::query()
			->select('id', 'name', 'sort', 'parent_id', 'is_dir', 'default_show_chapter_id')
			->where('document_id', $documentId)
			->orderBy('parent_id', 'asc')
			->orderBy('sort', 'asc')->get()->toArray();

		if (empty($list)) {
			return [];
		}

		$result = [];
		foreach ($list as $id => $item) {
			$item['is_dir'] = $item['is_dir'] == Chapter::IS_DIR ? true : false;
			$result[$item['id']] = $item;
			$result[$item['id']]['children'] = [];
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
				$v['children'] = $this->getTree($data, $v['id'], $i);
				$tree[] = $v;
				unset($data[$k]);
			}
		}
		return $tree;
	}

	/**
	 * 获取章节数据
	 * @param $id
	 * @param int $documentId
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
	 */
	public function getById($id, $documentId = 0)
	{
		$id = intval($id);
		$documentId = intval($documentId);

		$query = Chapter::query()->where('id', $id);
		if (!empty($documentId)) {
			$query = $query->where('document_id', $documentId);
		}

		return $query->first();
	}

	public function getMaxSort($parentId)
	{
		return Chapter::query()->where('parent_id', '=', $parentId)->max('sort');
	}

	public function deleteByDocumentId($documentId)
	{
		$chapterQuery = Chapter::query()->where('document_id', $documentId);

		$chapter = $chapterQuery->get();
		$chapterIds = $chapter->pluck('id')->toArray();

		if ($chapterQuery->delete()) {
			ChapterContent::query()->whereIn('chapter_id', $chapterIds)->delete();
			$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_COS,0);
			if ($setting) {
				CdnLogic::instance()->channel(SettingLogic::KEY_COS)->deletePath(sprintf('/%s', $documentId));
			}
		}
		return true;
	}

	public function deleteById($chapterId)
	{
		$chapter = $this->getById($chapterId);
		if (empty($chapter)) {
			throw new \RuntimeException('章节不存在');
		}

		if ($chapter->delete()) {
			Chapter::query()->where('default_show_chapter_id', '=', $chapterId)->update([
				'default_show_chapter_id' => 0
			]);
			ChapterContent::query()->where('chapter_id', '=', $chapterId)->delete();

			ChapterApi::query()->where('chapter_id', '=', $chapterId)->delete();
			ChapterApiParam::query()->where('chapter_id', '=', $chapterId)->delete();
			ChapterApiExtend::query()->where('chapter_id', '=', $chapterId)->delete();

			UserOperateLog::query()->where('document_id', '=', $chapter->document_id)->where('chapter_id', '=', $chapterId)->delete();

			StarLogic::instance()->clearByChapterId($chapterId);

			$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_COS,0);
			if ($setting) {
				CdnLogic::instance()->channel(SettingLogic::KEY_COS)->deletePath(sprintf('/%s/%s', $chapter->document_id, $chapterId));
			}

			return true;
		}

		throw new \RuntimeException('章节删除失败');
	}

	public function sortByChapter(Chapter $source, Chapter $target, $position = 'before')
	{
		if ($source->parent_id != $target->parent_id) {
			throw new \RuntimeException('文档不在一个目录内');
		}

		if ($source->document_id != $target->document_id) {
			throw new \RuntimeException('要移到的章节不在一个文档内');
		}

		if ($position == 'before') {
			//把大于target sort先全部后移一位，然后把当前插入到target后面
			Chapter::query()->where('document_id', '=', $source->document_id)
				->where('parent_id', '=', $target->parent_id)
				->where('id', '!=', $source->id)
				->where('sort', '>=', $target->sort)->increment('sort');

			$source->sort = $target->sort;
			$source->save();
		} else {
			//把大于target sort先全部后移一位，然后把当前插入到target后面
			Chapter::query()->where('document_id', '=', $source->document_id)
				->where('parent_id', '=', $target->parent_id)
				->where('id', '!=', $source->id)
				->where('sort', '>', $target->sort)->increment('sort');
			$source->sort = $target->sort + 1;
			$source->save();
		}

		return true;
	}

	public function moveByChapter(Chapter $source, Chapter $target)
	{
		if (!$target->is_dir) {
			throw new \RuntimeException('移动的目标不是目录，不能移动');
		}
		$documentChange = 0;
		if ($source->document_id != $target->document_id) {
			$documentChange = 1;
		}
		$source->document_id = $target->document_id;
		$source->parent_id = $target->id;
		$source->sort = ChapterLogic::instance()->getMaxSort($target->parent_id);
		$source->save();
		//下级目录修改
		if ($source->is_dir) {
			//移动的是一个目录
			$this->moveSunChapter($source, $source->document_id);
		}
		return true;
	}

	public function moveSunChapter($source, $documentId)
	{
		if ($source->is_dir) {
			//是目录
			$list = Chapter::query()->where('parent_id', $source->id)->get();
			if (count($list)) {
				foreach ($list as $key => $val) {
					$val->document_id = $documentId;
					$val->save();
					if ($val->is_dir) {
						$this->moveSunChapter($val, $documentId);
					}
				}
			}
		}
	}

	public function searchDocument($id, $keyword)
	{
		$content_ids = ChapterContent::where('content', 'like', '%' . $keyword . '%')->pluck('chapter_id')->toArray();
		$document_ids = Chapter::where('name', 'like', '%' . $keyword . '%')->where('document_id', $id)->pluck('id')->toArray();
		$document_ids = array_merge($content_ids, $document_ids);
		$documents = Chapter::whereIn('id', $document_ids)->where('document_id', $id)->get()->toArray();
		foreach ($documents as &$document) {
			$document['content'] = ChapterContent::find($document['id'])->content ?? '';
			if ($document['content']) {
				$document['content'] = mb_substr($document['content'], 0, 264, 'utf-8');
			}
			$document['path'] = $this->getPath($document['parent_id']);
		}

		$documentinfo = Document::where('id', $id)->first();
		if ($documentinfo && $documentinfo['creator_id']) {
			$userinfo = User::where('id', $documentinfo['creator_id'])->first();
			if ($userinfo) {
				foreach ($documents as $key => &$val) {
					$val['creator_id'] = $userinfo['id'];
					$val['username'] = $userinfo['username'];
				}
			}
		}

		return $documents;
	}

	public function getPath($parent_id)
	{
		$path = $parent_id;
		while ($parent_id != 0) {
			$temporary = Chapter::query()->find($parent_id)->first();
			if ($temporary) {
				$parent_id = $temporary->parent_id;
				$path = $parent_id . '/' . $path;
			} else {
				throw new \Exception('路径信息缺失!');
			}
		}
		return $path;
	}

	public function searchChapter($id, $keywords)
	{
		$chapter = Chapter::query()->select('id', 'parent_id', 'name')->where('document_id', $id)->where('name', 'like', '%' . $keywords . '%')->first();
		if ($chapter) {
			$chapter['content'] = ChapterContent::find($chapter['id'])->content ?? '';
			$chapter['path'] = $this->getPath($chapter['parent_id']);
			return $chapter;
		}
		throw new \Exception('没有匹配到任何章节');
	}
}
