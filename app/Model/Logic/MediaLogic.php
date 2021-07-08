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

use W7\App\Model\Entity\Media;
use W7\Core\Helper\Traiter\InstanceTraiter;

class MediaLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = Media::class;

	public function getByUnique($unique)
	{
		return Media::query()->where('unique', $unique)->first();
	}

	public function add($fileId, $url, $unique)
	{
		$row = $this->getByUnique($unique);
		if (!$row) {
			$data = [
				'fileid' => $fileId,
				'url' => $url,
				'unique' => $unique
			];
			return parent::store($data);
		} else {
			return $row;
		}
	}
}
