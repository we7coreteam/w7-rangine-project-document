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

use W7\App\Event\ChangeDocumentEvent;
use W7\App\Model\Entity\Chapter;
use W7\App\Model\Entity\ChapterContent;

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
		ChangeDocumentEvent::instance()->attach('id', $chapter->id)->dispatch();
		return $chapter;
	}

	public function updateChapter($id, $data)
	{
		$chapter = Chapter::find($id);
		if ($chapter) {
			if (APP_AUTH_ALL !== $data['auth'] && !in_array($chapter->document_id, $data['auth'])) {
				throw new \Exception('sorry,you are not authorized to modify this chapter!');
			}
			$chapter->name = $data['name'];
			$chapter->sort = $data['sort'];
			$chapter->save();
			ChangeDocumentEvent::instance()->attach('id', $id)->dispatch();
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
		$roots = Chapter::select('id', 'name', 'sort')->where('document_id', $id)->where('parent_id', 0)->orderBy('sort', 'desc')->get()->toArray();
		if ($roots) {
			foreach ($roots as $k=>$v) {
				$roots[$k]['children'] = [];
			}
			$this->getChild($roots);
		}
		return $roots;
	}

	public function getChild(&$chapters)
	{
		foreach ($chapters as $k=>$v) {
			$subordinates = Chapter::select('id', 'name', 'sort')->where('parent_id', $v['id'])->orderBy('sort', 'desc')->get()->toArray();
			foreach ($subordinates as $sk => $sv) {
				$subordinates[$sk]['children'] = [];
				$chapters[$k]['children'][] = $subordinates[$sk];
			}

			$this->getChild($chapters[$k]['children']);
		}
	}

	public function getChapter($document_id,$id)
	{
//		if (icache()->get('document_'.$id)) {
//			return $this->get('document_'.$id);
//		}
		$chapter = Chapter::where('id', $id)->where('document_id',$document_id)->first();
		if (!$chapter) {
			throw new \Exception('该章节不存在！');
		}
		$description = ChapterContent::where('chapter_id', $id)->first();
		if ($description) {
			$chapter['content'] = $description['content'];
		} else {
			$chapter['content'] = '';
		}
//		icache()->set('document_'.$id, $document, 24*3600);

		return $chapter;
	}

	public function searchDocument($keyword)
	{
		$document_ids = ChapterContent::where('content', 'like', '%'.$keyword.'%')->pluck('chapter_id')->toArray();
		$documents = Chapter::whereIn('id', $document_ids)->get()->toArray();
		foreach ($documents as &$document) {
			$document['content'] = ChapterContent::find($document['id'])->content ?? '';
		}

		return $documents;
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
			$chapter->delete();
			ChapterContent::destroy($id);
			ChangeDocumentEvent::instance()->attach('id', $id)->dispatch();
			return $chapter;
		}
		throw new \Exception('该章节不存在，请刷新页面');
	}

	public function saveContent($id, $content, $auth)
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
			$chapterContent->save();
			return $chapterContent;
		} else {
			ChapterContent::create(['chapter_id'=>$id,'content'=>$content]);
		}
	}

	public function getContent($id,$auth)
	{
		$document_id = Chapter::where('id', $id)->value('document_id');
		if (!$document_id) {
			throw new \Exception('该章节不存在，请刷新页面');
		}
		if (APP_AUTH_ALL !== $auth && !in_array($document_id, $auth)) {
			throw new \Exception('无权操作');
		}
		$chapterContent = ChapterContent::find($id);
		return $chapterContent->content;
	}

	public function searchChapter($id,$keywords)
	{
		$chapter = Chapter::select('id','parent_id','name')->where('document_id',$id)->where('name','like','%'.$keywords.'%')->first();
		if($chapter){
			$chapter['content'] = ChapterContent::find($chapter['id'])->content ?? '';
			return $chapter;
		}
		throw new \Exception('没有匹配到任何章节');
	}

	public function deleteDocument($document_id)
	{
		$chapters = Chapter::where('document_id',$document_id)->get();
		foreach($chapters as $chapter){
			ChapterContent::where('chapter_id',$chapter->id)->delete();
			$chapter->delete();
		}
		return true;
	}
}
