<?php

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\UserOperateLog;
use W7\Core\Helper\Traiter\InstanceTraiter;

class UserOperateLogic extends BaseLogic
{
	use InstanceTraiter;

	public function getByChapterAndOperate($chapterId, $operate)
	{
		return UserOperateLog::query()->where('chapter_id', '=', $chapterId)->where('operate', '=', $operate)->first();
	}

	public function clearByUid($userId)
	{
		return UserOperateLog::query()->where('user_id', '=', $userId)->delete();
	}

	public function clearByDocId($documentId)
	{
		return UserOperateLog::query()->where('document_id', '=', $documentId)->delete();
	}

	public function lists($where = [], $page = 1, $size = 20){
		$query = UserOperateLog::query()->with(['user','document','document.user'=>function($query){
			$query->select(['id','username','avatar']);
		}]);
		if (!empty($where['user_id']))	$query->where('user_id',$where['user_id']);
		if (!empty($where['operate']))	$query->whereIn('operate',$where['operate']);

		return $query->paginate($size,['*'],'',$page);
	}
}
