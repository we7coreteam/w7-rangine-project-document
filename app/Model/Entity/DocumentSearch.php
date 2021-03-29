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

namespace W7\App\Model\Entity;

class DocumentSearch extends BaseModel
{

	protected $table = 'document_search_hot';

	//搜索词
	public function getSearchWordAttribute(){
		return htmlspecialchars_decode($this->attributes['search_word'],ENT_QUOTES);
	}

}
