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

namespace W7\App\Controller\Admin;

use W7\App\Model\Logic\SettingLogic;
use W7\Http\Message\Server\Request;

class SettingController extends Controller
{
	public function show(Request $request)
	{
		try {
			$this->validate($request, [
				'key' => 'required',
			], [
				'key.required' => 'key必填',
			]);
			$setting = new SettingLogic();
			$res = $setting->show($request->input('key'));
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function save(Request $request)
	{
		try {
			$this->validate($request, [
				'key' => 'required',
				'app_id' => 'required',
				'secret_id' => 'required',
				'secret_key' => 'required',
				'bucket' => 'required',
				'region' => 'required',
				'cdn' => 'required',
			], [
				'key.required' => 'key必填',
				'app_id.required' => 'app_id必填',
				'secret_id.required' => 'secret_id必填',
				'secret_key.required' => 'secret_key必填',
				'bucket.required' => 'bucket必填',
				'region.required' => 'region必填',
				'cdn.required' => 'cdn必填',
			]);
			$data = [
				'app_id' => $request->input('app_id'),
				'secret_id' => $request->input('secret_id'),
				'secret_key' => $request->input('secret_key'),
				'bucket' => $request->input('bucket'),
				'region' => $request->input('region'),
				'cdn' => $request->input('cdn'),
			];
			$setting = new SettingLogic();
			$res = $setting->save($request->input('key'), $data);
			if ($res) {
				return $this->success($res);
			}
			return $this->error('保存失败');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
