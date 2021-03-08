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

use W7\App\Model\Entity\DocumentFeedback;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentFeedbackLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getByFeedbackDetail($id,$did)
	{
		$id = intval($id);
		if (empty($id)) {
			return [];
		}
		return DocumentFeedback::query()->where('id',$id)->where('document_id',$did)->first();
	}

	//更改反馈建议查看状态
    public function setByFeedbackStatus($id,$did){
		return DocumentFeedback::query()->where('id',$id)->where('document_id',$did)->update(['status'=>1]);
	}

    //查看是否有新的数据未读
	public function getByFeedbackNew()
	{
		return DocumentFeedback::query()->where('status', 0)->orderByDesc('created_at')->first();
	}
}
