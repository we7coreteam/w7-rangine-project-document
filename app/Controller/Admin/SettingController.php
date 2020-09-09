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

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\SettingLogic;
use W7\App\Model\Service\CdnLogic;
use W7\Http\Message\Server\Request;

class SettingController extends BaseController
{
	private $handler = [
		SettingLogic::KEY_COS => 'saveCos',
	];

	public function cos(Request $request)
	{
		$this->check($request);

		$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_COS);
		return $this->data([
			'key' => SettingLogic::KEY_COS,
			'setting' => $setting->setting,
		]);
	}

	public function save(Request $request)
	{
		$this->validate($request, [
			'key' => 'required',
		], [
			'key.required' => 'key必填',
		]);

		$key = $request->post('key');
		if (!isset($this->handler[$key])) {
			throw new ErrorHttpException('错误的配置key');
		}

		if (!empty($this->handler[$key])) {
			$value = call_user_func_array([$this, $this->handler[$key]], [$request]);
		} else {
			$value = $request->post('setting');
		}

		try {
			idb()->beginTransaction();
			SettingLogic::instance()->save($key, $value);
			CdnLogic::instance()->channel(SettingLogic::KEY_COS)->headBucket($value['bucket']);
			idb()->commit();
		} catch (\Throwable $e) {
			idb()->rollBack();
			if ($key == SettingLogic::KEY_COS) {
				ilogger()->channel('error')->error('云存储链接失败，请检查配置是否正确' . $e->getMessage());
				throw new ErrorHttpException('云存储链接失败，请检查配置是否正确');
			}
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data('success');
	}

	private function saveCos(Request $request)
	{
		$this->validate($request, [
			'setting.app_id' => 'required',
			'setting.secret_id' => 'required',
			'setting.secret_key' => 'required',
			'setting.bucket' => 'required',
			'setting.region' => 'required',
			'setting.url' => '',
			'setting.path' => 'sometimes|regex:/^\/[a-zA-Z\-_0-9]+$/i'
		], [
			'setting.app_id.required' => 'app_id必填',
			'setting.secret_id.required' => 'secret_id必填',
			'setting.secret_key.required' => 'secret_key必填',
			'setting.bucket.required' => 'bucket必填',
			'setting.region.required' => '所属地址必填',
			'setting.url' => '附件访问域名',
			'setting.path.regex' => '保存目录填写错误，格式例如：/savepath '
		]);

		$setting = $request->post('setting');

		$data = [
			'app_id' => $setting['app_id'],
			'secret_id' => $setting['secret_id'],
			'secret_key' => $setting['secret_key'],
			'bucket' => $setting['bucket'],
			'region' => $setting['region'],
			'url' => rtrim($setting['url'], '/'),
			'path' => rtrim($setting['path'], '/'),
		];

		if (empty($data['path'])) {
			$data['path'] = '';
		}

		return $data;
	}

	private function check(Request $request)
	{
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}
}
