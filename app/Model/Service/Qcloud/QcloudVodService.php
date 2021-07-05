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

namespace W7\App\Model\Service\Qcloud;

use W7\App\Model\Logic\SettingLogic;

class QcloudVodService
{
	/**
	 * 获取下载签名
	 **/
	public function makeVodDownloadSign($mediaUrl, $time = '')
	{
		$row = SettingLogic::instance()->getByKey(SettingLogic::KEY_VOD, 0);
		$key = $row->setting['key'];

		$dir = '/9d53cfb0vodcq1253494855/e77a1a8f5285890818866293110/';
		if (!$time) {
			$time = time() + 60 * 60 * 24;
		}
		$param = [
			't' => $time,
			'us' => time()
		];
		$sign = md5($key . $dir . $param['t'] . $param['us']);
		$param['sign'] = $sign;
		$original = http_build_query($param);

		return $mediaUrl . '?' . $original;
	}

	/**
	 * 获取前端上传签名
	 **/
	public function makeVodUploadSign()
	{
		$row = SettingLogic::instance()->getByKey(SettingLogic::KEY_VOD, 0);

		// 确定 App 的云 API 密钥
		$secret_id = $row->setting['secret_id'];
		$secret_key = $row->setting['secret_key'];
		// 确定签名的当前时间和失效时间
		$current = time();
		$expired = $current + 86400;  // 签名有效期：1天
		// 向参数列表填入参数
		$arg_list = array(
			'secretId' => $secret_id,
			'currentTimeStamp' => $current,
			'expireTime' => $expired,
			'random' => rand());
		// 计算签名
		$original = http_build_query($arg_list);
		$signature = base64_encode(hash_hmac('SHA1', $original, $secret_key, true) . $original);
		return $signature;
	}
}
