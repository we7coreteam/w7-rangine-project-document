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

namespace W7\App\Model\Service;

use Qcloud\Cos\Client;
use W7\Core\Database\LogicAbstract;
use W7\Core\Helper\Traiter\InstanceTraiter;

class CdnLogic extends LogicAbstract
{
	use InstanceTraiter;

	private $allowAttachService = [
		'cos' => qCloudCos::class,
	];

	public function get()
	{
		$this->connection('cos');
	}

	/**
	 * 上传一个文件
	 * @param $uploadPath 上传到CDN的路径
	 * @param $realPath 文件的本地真实路径
	 */
	public function uploadFile($uploadPath, $realPath)
	{
		if (!file_exists($realPath)) {
			throw new \RuntimeException('File not found');
		}

		return $this->connection('cos')->uploadFile($uploadPath, $realPath);
	}

	public function convertUrl($uploadPath)
	{
		return $this->connection('cos')->convertUrl($uploadPath);
	}

	private function connection($name)
	{
		if (empty($this->allowAttachService[$name])) {
			throw new \RuntimeException('Invalid connection name');
		}

		return iloader()->singleton($this->allowAttachService[$name]);
	}
}

/**
 * 腾讯云COS
 * Class qCloudCos
 * @package W7\App\Model\Service
 */
class qCloudCos
{
	private $secretId;
	private $secretKey;
	private $bucket;
	private $rootUrl;
	private $region = 'ap-shanghai';

	public function __construct()
	{
		$this->secretId = ienv('CDN_QCLOUD_COSV5_SECRET_ID');
		$this->secretKey = ienv('CDN_QCLOUD_COSV5_SECRET_KEY');
		$this->bucket = sprintf('%s-%s', ienv('CDN_QCLOUD_COSV5_BUCKET'), ienv('CDN_QCLOUD_COSV5_APP_ID'));
		$this->rootUrl = ienv('CDN_QCLOUD_COSV5_CDN');

		if (empty($this->secretKey) || empty($this->secretId)) {
			throw new \RuntimeException('Invalid cos config');
		}

		if (!empty($this->bucket)) {
			try {
				$isExistsBucket = $this->connection()->headBucket(
					[
						'Bucket' => $this->bucket,
					]
				);
			} catch (\Throwable $e) {
				throw new \RuntimeException($e->getMessage(), $e->getStatusCode());
			}
		}
	}

	/**
	 * @return Client
	 * @throws \Throwable
	 */
	public function connection()
	{
		try {
			$client = new Client(
				[
					'region' => $this->region,
					'schema' => 'https',
					'credentials' => [
						'secretId' => $this->secretId,
						'secretKey' => $this->secretKey,
					],
				]
			);
		} catch (\Throwable $e) {
			throw $e;
		}

		return $client;
	}

	public function uploadFile($uploadPath, $realPath)
	{
		try {
			$result = $this->connection()->putObject(
				[
					'Key' => $uploadPath,
					'Bucket' => $this->bucket,
					'Body' => fopen($realPath, 'rb'),
				]
			);
		} catch (\Throwable $e) {
			throw new \Exception($e->getMessage());
			throw new \RuntimeException($e->getMessage(), $e->getStatusCode());
		}

		return $this->replacePublicRootUrl($result['ObjectURL']);
	}

	public function convertUrl($uploadPath)
	{
		return sprintf('https://%s/%s', $this->rootUrl, $uploadPath);
	}

	private function replacePublicRootUrl($url)
	{
		$oldUrl = sprintf('%s.cos.%s.myqcloud.com', $this->bucket, $this->region);

		return str_replace($oldUrl, $this->rootUrl, $url);
	}
}
