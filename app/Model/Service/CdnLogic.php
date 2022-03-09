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
use W7\App\Model\Logic\SettingLogic;
use W7\Core\Database\LogicAbstract;
use W7\Core\Helper\Traiter\InstanceTraiter;

/**
 * Class CdnLogic
 * @package W7\App\Model\Service
 */
class CdnLogic extends LogicAbstract
{
	use InstanceTraiter;

	private $bucketSpace = [];
	private $client = [];
	/**
	 * 当前指定的通道
	 * @var string
	 */
	private $channel = '';

	public function __construct()
	{
	}

	/**
	 * @param string $channel
	 * @return mixed
	 * @throws \Throwable
	 */
	public function connection($channel = '')
	{
		if (empty($channel)) {
			$channel = $this->channel;
		}

		if (empty($channel)) {
			throw new \RuntimeException('Please set bucket');
		}

		if (empty($this->client[$channel])) {
			try {
				$this->client[$channel] = new Client([
					'region' => $this->bucketSpace[$channel]['region'],
					'schema' => 'https',
					'credentials' => [
						'secretId' => $this->bucketSpace[$channel]['secretId'],
						'secretKey' => $this->bucketSpace[$channel]['secretKey'],
					]
				]);
			} catch (\Throwable $e) {
				throw $e;
			}
		}
		return $this->client[$channel];
	}

	/**
	 * 设置当前存储的通道
	 * @param $channel
	 * @param bool $runTest
	 * @return $this
	 */
	public function channel($channel, $runTestBucket = false)
	{
		//从库里读取配置
		$setting = SettingLogic::instance()->getByKey($channel, 0);

		if (empty($setting)) {
			throw new \RuntimeException('请先配置上传参数');
		}

		$cosSetting = $setting->setting;
		$this->bucketSpace[$channel] = [
			'secretId' => $cosSetting['secret_id'],
			'secretKey' => $cosSetting['secret_key'],
			'bucket' => $cosSetting['bucket'],
			'rootUrl' => $cosSetting['url'],
			'region' => $cosSetting['region'],
			'rootPath' => $cosSetting['path'],
		];

		if (empty($channel) || empty($this->bucketSpace[$channel])) {
			throw new \RuntimeException('Invalid bucket name');
		}

		if (!empty($this->bucketSpace[$channel]['rootUrl'])) {
			$rootUrls = parse_url($this->bucketSpace[$channel]['rootUrl']);
			if (empty($rootUrls['host'])) {
				throw new \RuntimeException('Invalid root url');
			}
		}

		if ($runTestBucket) {
			try {
				//重置缓存，重新加载
				$this->client = [];
				$isExistsBucket = $this->connection($channel)->headBucket([
					'Bucket' => $this->bucketSpace[$channel]['bucket'],
				]);
			} catch (\Throwable $e) {
				throw new \RuntimeException($e->getMessage(), $e->getCode());
			}
		}

		$this->channel = $channel;

		return $this;
	}

	/**
	 * 上传一个文件
	 * @param 上传到COS的路径，以/开头 $uploadPath
	 * @param 文件在本地的物理绝对路径 $realPath
	 * @return string 文件在COS上的URL
	 */
	public function uploadFile($uploadPath, $realPath)
	{
		$uploadPath = $this->replacePublicRootPath($uploadPath);
		try {
			$result = $this->connection()->putObject([
				'Key' => $uploadPath,
				'Bucket' => $this->bucketSpace[$this->channel]['bucket'],
				'Body' => fopen($realPath, 'rb'),
			]);
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode());
		}
        $url = $result['Location'];
        if (!(strpos($url, 'http://') !== false || strpos($url, 'https://') !== false)) {
            $url = 'https://' . $url;
        }
        return $this->replacePublicRootUrl($url);
	}

	/**
	 * 获取目录下的所有文件
	 * @param $dir
	 * @return array
	 */
	public function getDirFiles($dir = ''): array
	{
		try {
			$dir = $this->replacePublicRootPath($dir);
			$dir = ltrim($dir, '/');

			$result = $this->connection()->listObjects([
				'Prefix' => $dir,
				'Bucket' => $this->bucketSpace[$this->channel]['bucket']
			]);
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode());
		}
		return (array)$result['Contents'];
	}

	/**
	 * 获取已上传文件的URL
	 * @param $uploadFile 文件存在cos上的路径
	 * @param $timeout 设置获取此地址的有效时间
	 * @return string cos的url
	 */
	public function getFileUrl($uploadFile, $timeout = null)
	{
		if (!is_null($timeout)) {
			$timeout = "+{$timeout} seconds";
		}
		$uploadFile = $this->replacePublicRootPath($uploadFile);
		try {
			$result = $this->connection()->getObjectUrl($this->bucketSpace[$this->channel]['bucket'], $uploadFile, $timeout);
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode());
		}

		return $result;
	}

	public function deleteFile($uploadFile)
	{
		if (empty($uploadFile)) {
			throw new \RuntimeException('Invalid file path');
		}

		if (!is_array($uploadFile)) {
			$uploadFile = [$uploadFile];
		}
		$objects = [];

		foreach ($uploadFile as $row) {
			$row = ltrim($row, '/');
			$objects[] = [
				'Key' => $this->replacePublicRootPath($uploadFile),
			];
		}

		try {
			$result = $this->connection()->deleteObjects([
				'Bucket' => $this->bucketSpace[$this->channel]['bucket'],
				'Objects' => $objects,
			]);
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode());
		}

		return true;
	}

	public function deletePath($path)
	{
		$pathList = $this->getDirFiles($path);
		if (empty($pathList)) {
			return true;
		}
		try {
			$result = $this->connection()->deleteObjects([
				'Bucket' => $this->bucketSpace[$this->channel]['bucket'],
				'Objects' => $pathList,
			]);
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode());
		}

		return true;
	}

	public function convertUrl($uploadPath, $returnOld = false)
	{
		$uploadPath = ltrim($uploadPath, '/');

		if (!empty($this->bucketSpace[$this->channel]['rootUrl']) && empty($returnOld)) {
			return sprintf('%s/%s', $this->bucketSpace[$this->channel]['rootUrl'], $uploadPath);
		}
		return sprintf('https://%s.cos.%s.myqcloud.com/%s', $this->bucketSpace[$this->channel]['bucket'], $this->bucketSpace[$this->channel]['region'], $uploadPath);
	}

	/**
	 * 替换COS的默认地址为用户设置过的根域名
	 * @param $url
	 * @return string
	 */
	private function replacePublicRootUrl($url)
	{
		if (empty($this->bucketSpace[$this->channel]['rootUrl'])) {
			return $url;
		}
		$oldUrl = $this->convertUrl('', true);
		$oldHost = parse_url($oldUrl, PHP_URL_HOST);
		$rootUrlHost = parse_url($this->bucketSpace[$this->channel]['rootUrl'], PHP_URL_HOST);

		return str_replace($oldHost, $rootUrlHost, $url);
	}

	/**
	 * 替换COS设置的统一根目录
	 */
	private function replacePublicRootPath($path)
	{
		return $this->bucketSpace[$this->channel]['rootPath'] . '/' . ltrim($path, '/');
	}
}
