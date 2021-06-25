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
use W7\App\Model\Service\UEditor\Uploader;
use W7\Http\Message\Server\Request;

class UploadController extends BaseController
{
	/**
	 * @api {post} /admin/upload/multipartUpload 切片上传
	 * @apiName multipartUpload
	 * @apiGroup Upload
	 *
	 * @apiParam {String} file_name 文件名称
	 * @apiParam {Number} part_number 当前分配ID
	 * @apiParam {Number} part_max 最大分片数量
	 * @apiParam {String} upload_id 上传ID part_number=1的时候会返回
	 * @apiParam {String} body 文件内容
	 */
	public function multipartUpload(Request $request)
	{
		$post = $this->validate($request, [
			'file_name' => 'required',
			'part_number' => 'required',
			'part_max' => 'required',
			'upload_id' => 'required',
			'file' => 'required',
		], [
			'file_name' => '文件名称',
			'part_number' => '分配ID',
			'part_max' => '最大分配数',
			'upload_id' => '上传ID',
			'file' => '文件内容',
		]);

		$path = time() . rand(1000, 9999) . '/' . $post['file_name'];
		if ($post['part_number'] == 1) {
			try {
				$post['upload_id'] = CdnLogic::instance()->channel(SettingLogic::KEY_COS)
					->createMultipartUpload($path);
			} catch (\Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
		} elseif (empty($post['upload_id'])) {
			throw new ErrorHttpException('没有上传ID');
		}
		//分片上传
		try {
			$file = $post['file'];
			$realPath = $file->getRealPath();
			$body = fopen($realPath, 'rb');
			$result = CdnLogic::instance()->channel(SettingLogic::KEY_COS)
				->uploadPart($path, $post['upload_id'], $body, $post['part_number']);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
		$parts = [];
		$multipartUploadCacheName = 'multipartUpload_' . $post['upload_id'];
		$multipartUploadCache = icache()->get($multipartUploadCacheName);
		if ($multipartUploadCache) {
			$parts = $multipartUploadCache;
		}
		$part = array('PartNumber' => $post['part_number'], 'ETag' => $result['ETag']);
		array_push($parts, $part);
		icache()->set($multipartUploadCacheName, $parts, 60 * 60);
		$updateBack = [
			'parts' => $parts,
			'upload_id' => $post['upload_id']
		];
		if ($post['part_max'] == $post['part_number']) {
			//如果当前是最后一片
			try {
				$end = CdnLogic::instance()->channel(SettingLogic::KEY_COS)
					->completeMultipartUpload($path, $post['upload_id'], $parts);
				$url = $end['Location'];
				if (!(strpos($url, 'http://') !== false || strpos($url, 'https://') !== false)) {
					$url = 'https://' . $url;
				}
				$updateBack['url'] = $url;
				icache()->set($multipartUploadCacheName, $parts, 0);
			} catch (\Throwable $e) {
				throw new ErrorHttpException($e->getMessage());
			}
		}

		return $this->data($updateBack);
	}

	public function image(Request $request)
	{
		$this->validate($request, [
			'file' => 'required|file'
		]);

		$file = $request->file('file');
		$size = $file->getSize();

		$mimeType = $file->getMimeType();
		$mimeTypeData = explode('/', $mimeType);
		//返回图片路径
		if ($mimeTypeData[0] != 'image') {
			throw new ErrorHttpException('上传的文件不是图片');
		}
		if ($size > 5242880) {
			throw new ErrorHttpException('请上传不大于5M的文件');
		}

		$fileName = sprintf('/%s.%s', irandom(32), pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
		try {
			$url = CdnLogic::instance()->channel(SettingLogic::KEY_COS)->uploadFile($fileName, $file->getRealPath());
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data(['url' => $url]);
	}

	public function uEditor(Request $request)
	{
		$action = $request->input('action', 'config');
		$config = iconfig()->get('ueditor.config');
		switch ($action) {
			case 'config':
				return $config;
			/* 上传图片 */
			case 'uploadimage':
				$uploadConfig = array(
					'pathFormat' => $config['imagePathFormat'],
					'maxSize' => $config['imageMaxSize'],
					'allowFiles' => $config['imageAllowFiles']
				);
				$fieldName = $config['imageFieldName'];
				$up = new Uploader($fieldName, $uploadConfig, 'upload');
				return $up->getFileInfo();
			default:
				throw new ErrorHttpException('不支持的操作');
		}
	}
}
