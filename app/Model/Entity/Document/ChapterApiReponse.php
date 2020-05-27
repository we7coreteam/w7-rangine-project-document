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

class ChapterApiReponse extends BaseModel
{
	public $timestamps = false;
	protected $table = 'document_chapter_api_reponse';
	protected $fillable = ['chapter_id', 'description'];
}
