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
	const KEY_THIRD_PARTY_LOGIN = 'third_party_login';
	const KEY_FORBID_WORDS = 'forbid_words';


	public function getByKey($key, $hide = 1)
	{
		$row = Setting::query()->where('key', $key)->first();
		if ($hide) {
			$row = $this->showHide($key, $row);
		}
		return $row;
	}

	public function showHide($key, $data)
	{
		if ($key == SettingLogic::KEY_COS) {
			$value = $data->setting;
			$value['app_id'] = $data->setting['app_id'] ? substr($data->setting['app_id'], 0, 3) . '***' . substr($data->setting['app_id'], -3, 3) : '';
			$value['secret_id'] = $data->setting['secret_id'] ? substr($data->setting['secret_id'], 0, 3) . '***' . substr($data->setting['secret_id'], -3, 3) : '';
			$value['secret_key'] = $data->setting['secret_key'] ? substr($data->setting['secret_key'], 0, 3) . '***' . substr($data->setting['secret_key'], -3, 3) : '';
			$value['url'] = $data->setting['url'] ? substr($data->setting['url'], 0, 3) . '***' . substr($data->setting['url'], -3, 3) : '';
			$data->value = json_encode($value);
			$data->setting = $value;
		} elseif ($key == SettingLogic::KEY_THIRD_PARTY_LOGIN) {
			$value = $data->setting;
			if (!empty($value['channel']) && $value['channel']) {
				foreach ($value['channel'] as $key => $val) {
					$value['channel'][$key]['setting']['app_id'] = $val['setting']['app_id'] ? substr($val['setting']['app_id'], 0, 3) . '***' . substr($val['setting']['app_id'], -3, 3) : '';
					$value['channel'][$key]['setting']['secret_key'] = $val['setting']['secret_key'] ? substr($val['setting']['secret_key'], 0, 3) . '***' . substr($val['setting']['secret_key'], -3, 3) : '';
					$value['channel'][$key]['setting']['access_token_url'] = $val['setting']['access_token_url'] ? substr($val['setting']['access_token_url'], 0, 3) . '***' . substr($val['setting']['access_token_url'], -3, 3) : '';
					$value['channel'][$key]['setting']['user_info_url'] = $val['setting']['user_info_url'] ? substr($val['setting']['user_info_url'], 0, 3) . '***' . substr($val['setting']['user_info_url'], -3, 3) : '';
				}
			}
			$data->value = json_encode($value);
			$data->setting = $value;
		}
		return $data;
	}

	public function save($key, $data)
	{
		$data = json_encode($data);

		$setting = $this->getByKey($key, 0);
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
