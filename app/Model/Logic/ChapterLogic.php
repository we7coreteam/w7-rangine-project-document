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

use W7\App\Event\ChangeChapterEvent;
use W7\App\Event\ChangeDocumentEvent;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\Chapter;
use W7\App\Model\Entity\Document\ChapterContent;
use W7\App\Model\Entity\User;

class ChapterLogic extends BaseLogic
{
	public function createChapter($data)
	{
		if ($data['parent_id'] == 0) {
			$data['levels'] = 1;
		} else {
			$parent = Chapter::find($data['parent_id']);
			if (!$parent) {
				throw new \Exception('父章节不存在');
			}
			$data['levels'] = $parent->levels + 1;
			if ($data['levels'] > 3) {
				throw new \Exception('章节最大层级为３层！');
			}
		}
		$chapter = Chapter::create($data);
		ChangeChapterEvent::instance()->attach('chapter', $chapter)->dispatch();
		return $chapter;
	}

	public function updateChapter($id, $data)
	{
		$chapter = Chapter::find($id);
		if ($chapter) {
			if (APP_AUTH_ALL !== $data['auth'] && !in_array($chapter->document_id, $data['auth'])) {
				throw new \Exception('无权操作!');
			}
			$chapter->name = $data['name'];
			$chapter->sort = $data['sort'];
			$chapter->save();
			ChangeChapterEvent::instance()->attach('chapter', $chapter)->dispatch();
			return $chapter;
		}
		throw new \Exception('this chapter is not exist,please refresh the web page!');
	}

	public function publishOrCancel($id, $is_show)
	{
		$document = Chapter::find($id);
		if ($document) {
			$document->is_show = $is_show;
			$document->save();
			ChangeDocumentEvent::instance()->attach('id', $id)->dispatch();

			return true;
		}

		throw new \Exception('该文档不存在');
	}

	public function getChapters($id)
	{
		$cacheData = cache()->get('chapters_'.$id);
		if ($cacheData) {
			return $cacheData;
		} else {
			$roots = Chapter::select('id', 'name', 'sort')->where('document_id', $id)->where('parent_id', 0)->orderBy('sort', 'asc')->get()->toArray();
			if ($roots) {
				foreach ($roots as $k=>$v) {
					$roots[$k]['children'] = [];
				}
				$this->getChild($roots);
			}
			cache()->set('chapters_'.$id, $roots);
			return $roots;
		}
	}

	public function getChild(&$chapters)
	{
		foreach ($chapters as $k=>$v) {
			$subordinates = Chapter::select('id', 'name', 'sort')->where('parent_id', $v['id'])->orderBy('sort', 'asc')->get()->toArray();
			foreach ($subordinates as $sk => $sv) {
				$subordinates[$sk]['children'] = [];
				$chapters[$k]['children'][] = $subordinates[$sk];
			}

			$this->getChild($chapters[$k]['children']);
		}
	}

	public function getChapter($document_id, $id)
	{
		if (cache()->get('chapter_'.$id)) {
			return cache()->get('chapter_'.$id);
		} else {
			$chapter = Chapter::where('id', $id)->where('document_id', $document_id)->first();
			if (!$chapter) {
				throw new \Exception('该章节不存在！');
			}
			$document = Document::where('id', $document_id)->first();
			if ($document && $document['creator_id']){
				$userinfo = User::where('id', $document['creator_id'])->first();
				if ($userinfo){
					$chapter['creator_id'] = $userinfo['id'];
					$chapter['username'] = $userinfo['username'];
				}
			}
			$description = ChapterContent::where('chapter_id', $id)->first();
			if ($description) {
				$chapter['content'] = $description['content'];
				$chapter['layout'] = $description['layout'];
			} else {
				$chapter['content'] = '';
			}
			$previous = $this->previousChapter($chapter);
			$chapter['previous_chapter_id'] = $previous['id'];
			$chapter['previous_chapter_name'] = $previous['name'];
			$next = $this->nextChapter($chapter);
			$chapter['next_chapter_id'] = $next['id'];
			$chapter['next_chapter_name'] = $next['name'];
			cache()->set('chapter_'.$id, $chapter, 24*3600);
			return $chapter;
		}
	}

	public function previousChapter($chapter)
	{
		$parent_id = $chapter['parent_id'];
		$sort = $chapter['sort'];
		$elderBrother = Chapter::where('parent_id', $parent_id)->where('sort', '>', $sort)->orderBy('sort')->first();
		if ($elderBrother) {
			return $elderBrother;
		}
		if ($parent_id) {
			$parent = Chapter::find($parent_id);
			if ($parent) {
				return $parent;
			}
		}
		return ['id'=>0,'name'=>''];
	}

	public function nextChapter($chapter)
	{
		$parent_id = $chapter['parent_id'];
		$sort = $chapter['sort'];
		$id = $chapter['id'];
		$child = Chapter::where('parent_id', $id)->orderBy('sort', 'asc')->first();
		if ($child) {
			return $child;
		}
		$youngerBrother = Chapter::where('parent_id', $parent_id)->where('sort', '<', $sort)->orderBy('sort', 'asc')->first();
		if ($youngerBrother) {
			return $youngerBrother;
		}
		return ['id'=>0,'name'=>''];
	}

	public function searchDocument($id, $keyword)
	{
		$document_ids = ChapterContent::where('content', 'like', '%'.$keyword.'%')->pluck('chapter_id')->toArray();
		$documents = Chapter::whereIn('id', $document_ids)->where('document_id', $id)->get()->toArray();
		foreach ($documents as &$document) {
			$document['content'] = ChapterContent::find($document['id'])->content ?? '';
			if ($document['content']){
				$document['content'] = substr($document['content'],0,780);
			}
			$document['layout'] = ChapterContent::find($document['id'])->layout;
			$document['path'] = $this->getPath($document['parent_id']);
		}
		$documentinfo = Document::where('id', $id)->first();
		if ($documentinfo && $documentinfo['creator_id']){
			$userinfo = User::where('id', $documentinfo['creator_id'])->first();
			if ($userinfo){
				$document['creator_id'] = $userinfo['id'];
				$document['username'] = $userinfo['username'];
			}
		}

		return $documents;
	}

	public function getPath($parent_id)
	{
		$path = $parent_id;
		while ($parent_id != 0) {
			$temporary = Chapter::find($parent_id)->first();
			if ($temporary) {
				$parent_id = $temporary->parent_id;
				$path = $parent_id.'/'.$path;
			} else {
				throw new \Exception('路径信息缺失!');
			}
		}
		return $path;
	}

	public function deleteChapter($id, $auth)
	{
		if (Chapter::where('parent_id', $id)->count() > 0) {
			throw new \Exception('该章节下有子章节，不可删除！');
		}
		$chapter = Chapter::find($id);
		if ($chapter) {
			if (APP_AUTH_ALL !== $auth && !in_array($chapter->document_id, $auth)) {
				throw new \Exception('无权操作');
			}
			$resChapter = $chapter->delete();
			$resChapterContent = ChapterContent::destroy($id);
			ChangeChapterEvent::instance()->attach('chapter', $chapter)->dispatch();
			if ($resChapter && $resChapterContent){
				return $chapter;
			}else{
				return false;
			}
		}
		throw new \Exception('该章节不存在，请刷新页面');
	}

	public function saveContent($id, $content, $layout, $auth)
	{
		$document_id = Chapter::where('id', $id)->value('document_id');
		if (!$document_id) {
			throw new \Exception('该章节不存在，请刷新页面');
		}
		if (APP_AUTH_ALL !== $auth && !in_array($document_id, $auth)) {
			throw new \Exception('无权操作');
		}
		$chapterContent = ChapterContent::find($id);
		if ($chapterContent) {
			$chapterContent->content = $content;
			$chapterContent->layout = $layout;
			$chapterContent->save();
			return $chapterContent;
		} else {
			ChapterContent::create(['chapter_id'=>$id,'content'=>$content,'layout'=>$layout]);
		}
	}

	public function getContent($id, $auth)
	{
		$documentinfo = Chapter::where('id', $id)->first();
		if (!$documentinfo || !$documentinfo['document_id']) {
			throw new \Exception('该章节不存在，请刷新页面');
		}
		if (APP_AUTH_ALL !== $auth && !in_array($documentinfo['document_id'], $auth)) {
			throw new \Exception('无权操作');
		}
		$chapter = ChapterContent::find($id);
		if (!$chapter){
			return $chapter;
		}
		$document = Document::where('id', $documentinfo['document_id'])->first();
		if ($document && $document['creator_id']){
			$userinfo = User::where('id', $document['creator_id'])->first();
			if ($userinfo){
				$chapter['creator_id'] = $userinfo['id'];
				$chapter['username'] = $userinfo['username'];
			}
		}
		$chapter['created_at'] = $documentinfo['created_at'];
		$chapter['updated_at'] = $documentinfo['updated_at'];
		return $chapter;
	}

	public function searchChapter($id, $keywords)
	{
		$chapter = Chapter::select('id', 'parent_id', 'name')->where('document_id', $id)->where('name', 'like', '%'.$keywords.'%')->first();
		if ($chapter) {
			$chapter['content'] = ChapterContent::find($chapter['id'])->content ?? '';
			$chapter['path'] = $this->getPath($chapter['parent_id']);
			return $chapter;
		}
		throw new \Exception('没有匹配到任何章节');
	}

	public function deleteDocument($document_id)
	{
		$chapters = Chapter::where('document_id', $document_id)->get();
		foreach ($chapters as $chapter) {
			ChapterContent::where('chapter_id', $chapter->id)->delete();
			$chapter->delete();
		}
		return true;
	}
}
