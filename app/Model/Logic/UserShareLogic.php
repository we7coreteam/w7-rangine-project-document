<?php

namespace W7\App\Model\Logic;

use W7\Core\Helper\Traiter\InstanceTraiter;

class UserShareLogic extends BaseLogic
{
	use InstanceTraiter;

	const SHARE_KEY = '';

	public function getShareUrl($userId, $documentId, $chapterId)
	{
		return rtrim(ienv('API_HOST'), '/') . '/chapter/' . $documentId. '?id=' . $chapterId . '&share_key=' . base64_encode(authcode($userId . '-' . $chapterId, 'ENCODED', self::SHARE_KEY));
	}

	public function getUidAndChapterByShareKey($shareKey)
	{
		$data = base64_decode(authcode($shareKey, 'DECODE', self::SHARE_KEY));
		$data = explode('-', $data);
		if (count($data) != 2) {
			throw new \RuntimeException('share key error');
		}

		return $data;
	}
}