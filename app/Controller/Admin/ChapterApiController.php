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
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\Http\Message\Server\Request;

class ChapterApiController extends BaseController
{
	/**
	 * @api {get} /chapterapi/getStatusCode 获取状态码列表
	 * @apiName getStatusCode
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {200,301,403,404,500,502,503,504}
	 */
	public function getStatusCode(Request $request)
	{
		return ChapterApi::getStatusCode();
	}

	/**
	 * @api {get} /chapterapi/getMethodLabel 获取请求类型列表
	 * @apiName getMethodLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"1":"GET","2":"POST","3":"PUT","4":"PATCH","5":"DELETE","6":"COPY","7":"HEAD","8":"PTIONS","9":"LINK","10":"UNLINK","11":"PURGE","12":"LOCK","13":"UNLOCK","14":"PROPFIND","15":"VIEW"}
	 */
	public function getMethodLabel(Request $request)
	{
		return ChapterApi::getMethodLabel();
	}

	/**
	 * @api {get} /chapterapi/getEnabledLabel 获取必填类型列表
	 * @apiName getEnabledLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"1":"False","2":"Ture"}
	 */
	public function getEnabledLabel(Request $request)
	{
		return ChapterApiParam::getEnabledLabel();
	}
}
