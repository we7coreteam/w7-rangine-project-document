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
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\SettingLogic;
use W7\App\Model\Service\CdnLogic;
use W7\Http\Message\Server\Request;

class UploadController extends BaseController
{
	private $path;
	private $uploader;

	public function __construct()
	{
		$setting = SettingLogic::instance()->getByKey(SettingLogic::KEY_COS);
		$cosSetting = $setting->setting;

		$this->uploader = new CdnLogic([
			'secretId' => $cosSetting['secret_id'],
			'secretKey' => $cosSetting['secret_key'],
			'bucket' => $cosSetting['bucket'],
			'rootUrl' => $cosSetting['url'],
			'region' => $cosSetting['region'],
		], 'cos');
		$this->path = $cosSetting['path'];
	}

	public function image(Request $request)
	{
		$this->validate($request, [
			'file' => 'required|file|mimes:bmp,png,jpeg,jpg|max:2048',
			'chapter_id' => 'required',
			'document_id' => 'required',
		]);

		$user = $request->getAttribute('user');
		if (!$user->isManager && !$user->isFounder && !$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapterId = intval($request->post('chapter_id'));
		$documentId = intval($request->post('document_id'));

		$chapter = ChapterLogic::instance()->getById($chapterId);
		if (empty($chapter) || $chapter->document_id != $documentId) {
			throw new ErrorHttpException('章节不存在');
		}

		$file = $request->file('file');

		$fileName = sprintf('%s/%s/%s/%s.%s', $this->path, $chapter->document_id, $chapterId, irandom(32), pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
		try {
			$url = $this->uploader->channel('cos')->uploadFile($fileName, $file->getTmpFile());
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data(['url' => $url]);
	}

	public function index(Request $request)
	{
		$this->validate($request, [
			'action' => 'required',
		], [
			'action.required' => 'action必填',
		]);
		$action = $request->input('action');
		if ($action == 'config') {
			$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', file_get_contents(BASE_PATH.'/config/ueditor.json')), true);
			return $CONFIG;
		}
		throw new ErrorHttpException('action值有误');
	}
}
