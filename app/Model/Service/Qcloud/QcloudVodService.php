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
use GuzzleHttp\Client;

class QcloudVodService
{
	protected $secretId;
	protected $secretKey;
	protected $host = 'vod.tencentcloudapi.com';

	public function __construct()
	{
		$row = SettingLogic::instance()->getByKey(SettingLogic::KEY_VOD, 0);
		$this->secretId = $row->setting['secret_id'];
		$this->secretKey = $row->setting['secret_key'];
	}

	/**
	 * 获取下载签名
	 **/
	public function makeVodDownloadSign($mediaUrl, $time = '')
	{
		$row = SettingLogic::instance()->getByKey(SettingLogic::KEY_VOD, 0);
		$qcloudKey = $row->setting['key'];

		$dir = '/';
		$data = explode('/', $mediaUrl);
		foreach ($data as $k => $v) {
			if ($k > 2) {
				$dir = $dir . $v . '/';
			}
			if ($k == count($data) - 2) {
				break;
			}
		}

		if (!$time) {
			$time = time() + 60 * 60 * 24;
		}
		$param = [
			't' => $time,
			'us' => time()
		];
		$sign = md5($qcloudKey . $dir . $param['t'] . $param['us']);
		$param['sign'] = $sign;
		$original = http_build_query($param);

		return $mediaUrl . '?' . $original;
	}

	/**
	 * 获取前端上传签名
	 **/
	public function makeVodUploadSign()
	{
		// 确定签名的当前时间和失效时间
		$current = time();
		$expired = $current + 86400;  // 签名有效期：1天
		// 向参数列表填入参数
		$arg_list = array(
			'secretId' => $this->secretId,
			'currentTimeStamp' => $current,
			'expireTime' => $expired,
			'random' => rand());
		// 计算签名
		$original = http_build_query($arg_list);
		$signature = base64_encode(hash_hmac('SHA1', $original, $this->secretKey, true) . $original);
		return $signature;
	}

	public function makeTranscode($fileId)
	{
		$templateId = 10010; //模板id
		$param = [
			'Nonce' => rand(),
			'Timestamp' => time(),
			'SecretId' => $this->secretId,
			'Version' => '2018-07-17',
			'Action' => 'ProcessMedia',
			'FileId' => $fileId,
			'MediaProcessTask.TranscodeTaskSet.0.Definition' => $templateId
		];
		ksort($param);
		$srcStr = 'GET' . $this->host . '/?' . http_build_query($param);
		$signature = base64_encode(hash_hmac('SHA1', $srcStr, $this->secretKey, true));
		$param['Signature'] = $signature;
		$requestStr = 'https://' . $this->host . '/?' . http_build_query($param);
		$client = new Client();
		$response = $client->get($requestStr);
		return $response->getBody();
	}
}
