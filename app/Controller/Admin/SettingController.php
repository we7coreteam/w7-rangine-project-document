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
				'url' => 'sometimes|url',
				'path' => 'sometimes|regex:/^\/[a-zA-Z\-_0-9]+$/i'
			], [
				'key.required' => 'key必填',
				'app_id.required' => 'app_id必填',
				'secret_id.required' => 'secret_id必填',
				'secret_key.required' => 'secret_key必填',
				'bucket.required' => 'bucket必填',
				'region.required' => '所属地址必填',
				'url.url' => '附件访问域名格式错误',
				'path.regex' => '保存目录填写错误，格式例如：/savepath '
			]);
			$data = [
				'app_id' => $request->input('app_id'),
				'secret_id' => $request->input('secret_id'),
				'secret_key' => $request->input('secret_key'),
				'bucket' => $request->input('bucket'),
				'region' => $request->input('region'),
				'url' => rtrim($request->input('url'), '/'),
				'path' => rtrim($request->input('path'), '/'),
			];

			if (empty($data['url'])) {
				$data['url'] = sprintf('https://%s-%s.cos.%s.myqcloud.com', $data['bucket'], $data['app_id'], $data['region']);
			}
print_r($data);exit;
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
