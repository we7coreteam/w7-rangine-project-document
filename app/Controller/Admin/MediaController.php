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
use W7\App\Model\Logic\MediaLogic;
use W7\Http\Message\Server\Request;

class MediaController extends BaseController
{
	/**
	 * @api {post} /admin/media 媒体-新增媒体
	 * @apiName store
	 * @apiGroup media
	 *
	 * @apiParam {String} unique 媒体唯一值（文件MD5）
	 * @apiParam {String} fileid 媒体id
	 * @apiParam {String} url 媒体url
	 */
	public function store(Request $request)
	{
		$data = $this->validate($request, [
			'unique' => 'required',
			'fileid' => 'required',
			'url' => 'required',
		], [], [
			'unique' => '文件MD5',
			'fileid' => '媒体id',
			'url' => '媒体url',
		]);
		$re = MediaLogic::instance()->add($data['fileid'], $data['url'], $data['unique']);
		return $this->data($re);
	}
}
