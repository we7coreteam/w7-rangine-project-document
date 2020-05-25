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

namespace W7\App\Model\Entity\Document;

use W7\App\Model\Entity\BaseModel;

class ChapterContent extends BaseModel
{
	public $timestamps = false;
	protected $table = 'document_chapter_content';
	protected $primaryKey = 'chapter_id';

	// 数据来源类型
	const LAYOUT_MARKDOWM = 0;//MARKDOWN
	const LAYOUT_HTTP = 1;//HTTP请求
}
