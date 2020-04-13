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

class ChapterApi extends BaseModel
{
	protected $table = 'document_chapter_api';
	protected $fillable = ['chapter_id', 'url', 'method', 'status_code', 'description', 'body_param_location'];

	const METHOD_GET = 1;
	const METHOD_POST = 2;
	const METHOD_PUT = 3;
	const METHOD_PATCH = 4;
	const METHOD_DELETE = 5;
	const METHOD_COPY = 6;
	const METHOD_HEAD = 7;
	const METHOD_PTIONS = 8;
	const METHOD_LINK = 9;
	const METHOD_UNLINK = 10;
	const METHOD_PURGE = 11;
	const METHOD_LOCK = 12;
	const METHOD_UNLOCK = 13;
	const METHOD_PROPFIND = 14;
	const METHOD_VIEW = 15;
}
