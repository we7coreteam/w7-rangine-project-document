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


use W7\App\Model\Entity\Document\ChapterApiData;
use W7\App\Model\Logic\BaseLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ChapterApiDataLogic extends BaseLogic
{
	use InstanceTraiter;

	public function deleteChapterApiData($respondId,$chapter_id){
		$checkData = ChapterApiData::query()->where(['id'=>$respondId,'chapter_id'=>$chapter_id])->first();
		if ($checkData){
			return ChapterApiData::query()->where(['id'=>$respondId,'chapter_id'=>$chapter_id])->delete();
		}
		return true;
	}

	public function getByChapterApiData($chapter_id){
		return ChapterApiData::query()->where('chapter_id',$chapter_id)->get();
	}


}
