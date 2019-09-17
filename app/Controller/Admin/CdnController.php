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

use W7\App\Model\Logic\CdnLogic;
use W7\Http\Message\Server\Request;

class CdnController extends Controller
{
	public function index()
	{
		try {
			$cdn = new CdnLogic();
			$res = $cdn->index();
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function save(Request $request)
	{
		try {
			$this->validate($request, [
				'CDN_QCLOUD_COSV5_SECRET_ID' => 'required',
				'CDN_QCLOUD_COSV5_SECRET_KEY' => 'required',
				'CDN_QCLOUD_COSV5_APP_ID' => 'required',
				'CDN_QCLOUD_COSV5_BUCKET' => 'required',
				'CDN_QCLOUD_COSV5_CDN' => 'required',
			], [
				'CDN_QCLOUD_COSV5_SECRET_ID.required' => 'CDN_QCLOUD_COSV5_SECRET_ID必填',
				'CDN_QCLOUD_COSV5_SECRET_KEY.required' => 'CDN_QCLOUD_COSV5_SECRET_KEY必填',
				'CDN_QCLOUD_COSV5_APP_ID.required' => 'CDN_QCLOUD_COSV5_APP_ID必填',
				'CDN_QCLOUD_COSV5_BUCKET.required' => 'CDN_QCLOUD_COSV5_BUCKET必填',
				'CDN_QCLOUD_COSV5_CDN.required' => 'CDN_QCLOUD_COSV5_CDN必填',
			]);
			$data = [
				'CDN_QCLOUD_COSV5_SECRET_ID' => $request->input('CDN_QCLOUD_COSV5_SECRET_ID'),
				'CDN_QCLOUD_COSV5_SECRET_KEY' => $request->input('CDN_QCLOUD_COSV5_SECRET_KEY'),
				'CDN_QCLOUD_COSV5_APP_ID' => $request->input('CDN_QCLOUD_COSV5_APP_ID'),
				'CDN_QCLOUD_COSV5_BUCKET' => $request->input('CDN_QCLOUD_COSV5_BUCKET'),
				'CDN_QCLOUD_COSV5_CDN' => $request->input('CDN_QCLOUD_COSV5_CDN'),
			];
			$cdn = new CdnLogic();
			$res = $cdn->save($data);
			if ($res) {
				return $this->success($res);
			}
			return $this->error('保存失败');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
