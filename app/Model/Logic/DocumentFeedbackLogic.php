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

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentFeedback;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentFeedbackLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getByFeedbackDetail($id)
	{
		$id = intval($id);
		if (empty($id)) {
			return [];
		}
		return DocumentFeedback::query()->find($id);
	}

	//更改反馈建议查看状态
    public function setByFeedbackStatus($id){
		return DocumentFeedback::query()->where('id',$id)->update(['status'=>1]);
	}

    //查看是否有新的数据未读
	public function getByFeedbackNew()
	{
		return Document::query()->where('status', 0)->orderByDesc('created_at')->first();
	}
}
