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

use W7\App\Model\Entity\Setting;
use W7\Core\Helper\Traiter\InstanceTraiter;

class SettingLogic extends BaseLogic
{
	use InstanceTraiter;

	const KEY_COS = 'cloud_cosv5';

	public function getByKey($key)
	{
		$row = Setting::query()->where('key', $key)->first();
		return $row;
	}

	public function save($key, $data)
	{
		$data = json_encode($data);

		$setting = $this->getByKey($key);
		if (empty($setting)) {
			Setting::query()->create([
				'key' => $key,
				'value' => $data
			]);
		} else {
			$setting->value = $data;
			$setting->save();
		}
		return true;
	}
}
