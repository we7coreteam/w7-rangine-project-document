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

use W7\App\Model\Entity\Cdn;

class CdnLogic extends BaseLogic
{
	public function index()
	{
		$res = Cdn::query()->where('key', 'cdn')->first();
		return $this->handleData($res);
	}

	public function save($data)
	{
		$data = json_encode($data);
		$res = Cdn::query()->where('key', 'cdn')->first();
		if ($res) {
			$res = Cdn::query()->update(['key' => 'cdn','value' => $data]);
			if ($res) {
				$res = ['value' => $data];
			}
		} else {
			$res = Cdn::query()->create(['key' => 'cdn','value' => $data]);
		}
		return $this->handleData($res);
	}

	public function handleData($data)
	{
		if ($data && isset($data['value'])) {
			return ['key'=>'cdn','value'=>json_decode($data['value'], true)];
		}
		return $data;
	}
}
