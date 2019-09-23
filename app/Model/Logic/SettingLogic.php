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

class SettingLogic extends BaseLogic
{
	public function show($key)
	{
		$res = Setting::query()->where('key', $key)->first();
		return $this->handleData($key,$res);
	}

	public function save($key,$data)
	{
		$data = json_encode($data);
		$res = Setting::query()->where('key', $key)->first();
		if ($res) {
			$res = Setting::query()->update(['key' => $key,'value' => $data]);
			if ($res) {
				$res = ['value' => $data];
			}
		} else {
			$res = Setting::query()->create(['key' => $key,'value' => $data]);
		}
		return $this->handleData($key,$res);
	}

	private function handleData($key,$data)
	{
		if ($data && isset($data['value'])) {
			return ['key'=>$key,'value'=>json_decode($data['value'], true)];
		}
		return $data;
	}
}
