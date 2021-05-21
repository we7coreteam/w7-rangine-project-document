<?php

/**
 * WeEngine Team
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Model\Entity\Message;

use W7\App\Model\Entity\BaseModel;

class Text extends BaseModel
{
	protected $table = 'message_text';
	protected $fillable = ['title', 'content'];
}
