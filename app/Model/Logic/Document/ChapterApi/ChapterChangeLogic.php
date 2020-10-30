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

namespace W7\App\Model\Logic\Document\ChapterApi;

//返回演示数据demo
class ChapterChangeLogic extends ChapterCommonLogic
{
	public function keyWordToData($keyWord)
	{
		$data1=explode("\n",$keyWord);
		if(count($data1)){
			foreach ($data1 as $key =>$val){
				foreach ($val as $k=>$v){

				}
			}
		}

		$data = $keyWord;

		dump($data);
		return [];
	}
}
