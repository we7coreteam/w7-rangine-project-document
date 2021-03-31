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

use W7\App\Model\Entity\DocumentSearch;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentSearchLogic extends BaseLogic
{
	use InstanceTraiter;

	//记录搜索热词
	public function addSearchHotWord($word){
		$word = trim($word);
		if ($word){
			$word = htmlspecialchars($word,ENT_QUOTES);
			DocumentSearch::query()->create([
				'search_word' => $word
			]);
		}
	}


	//获取搜索热词列表
	public function getSearchHotList($limit=20){
		$result = DocumentSearch::query()->selectRaw("search_word,count(*) as count")
			                             ->groupBy('search_word')
			                             ->orderBy('count','desc')
			                             ->limit($limit)->get()->toArray();
		$result = array_column($result,'search_word');
		return $result;
	}
}
