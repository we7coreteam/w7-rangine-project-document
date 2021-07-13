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
	protected $config;
	protected $host = 'vod.tencentcloudapi.com';

	public function __construct()
	{
		$row = SettingLogic::instance()->getByKey(SettingLogic::KEY_VOD, 0);
		$this->config = $row->setting;
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
			'secretId' => $this->config['secret_id'],
			'currentTimeStamp' => $current,
			'expireTime' => $expired,
			'random' => rand());
		// 计算签名
		$original = http_build_query($arg_list);
		$signature = base64_encode(hash_hmac('SHA1', $original, $this->config['secret_key'], true) . $original);
		return $signature;
	}

	public function makeTranscode($fileId)
	{
		$templateId = 10010; //模板id
		$param = [
			'Nonce' => rand(),
			'Timestamp' => time(),
			'SecretId' => $this->config['secret_id'],
			'Version' => '2018-07-17',
			'Action' => 'ProcessMedia',
			'FileId' => $fileId,
			'MediaProcessTask.TranscodeTaskSet.0.Definition' => $templateId
		];
		$response = $this->buildRequest($param);
		return $response->getBody();
	}

	public function checkConnect()
	{
		$param = [
			'Nonce' => rand(),
			'Timestamp' => time(),
			'SecretId' => $this->config['secret_id'],
			'Version' => '2018-07-17',
			'Action' => 'ApplyUpload',
			'Region' => $this->config['region'],
			'MediaType' => 'mp4'
		];

		$response = json_decode($this->buildRequest($param)->getBody(), true);
		if (isset($response['Response']['Error'])) {
			throw new \RuntimeException($response['Response']['Error']['Message'], $response['Response']['Error']['Code']);
		}
		return $response;
	}

	public function buildRequest($param)
	{
		ksort($param);
		$srcStr = 'GET' . $this->host . '/?' . http_build_query($param);
		$signature = base64_encode(hash_hmac('SHA1', $srcStr, $this->config['secret_key'], true));
		$param['Signature'] = $signature;
		$requestStr = 'https://' . $this->host . '/?' . http_build_query($param);
		try {
			$client = new Client();
			$response = $client->get($requestStr);
			return $response;
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode());
		}
	}
}
