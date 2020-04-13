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

namespace W7\App\Controller\Admin\Document;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Document\ChapterApiLogic;
use W7\App\Model\Logic\Document\ChapterApiParamLogic;
use W7\Http\Message\Server\Request;

class ChapterApiController extends BaseController
{
	/**
	 * @api {get} /document/chapterapi/getStatusCode 获取状态码列表
	 * @apiName getStatusCode
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {200,301,403,404,500,502,503,504}
	 */
	public function getStatusCode(Request $request)
	{
		$chapterApiLogic = new ChapterApiLogic();
		return $chapterApiLogic->getStatusCode();
	}

	/**
	 * @api {get} /document/chapterapi/getMethodLabel 获取请求方式列表
	 * @apiName getMethodLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"label":"请求方式","option":[{"label":"GET","value":1},{"label":"POST","value":2},{"label":"PUT","value":3},{"label":"PATCH","value":4},{"label":"DELETE","value":5},{"label":"COPY","value":6},{"label":"HEAD","value":7},{"label":"PTIONS","value":8},{"label":"LINK","value":9},{"label":"UNLINK","value":10},{"label":"PURGE","value":11},{"label":"LOCK","value":12},{"label":"UNLOCK","value":13},{"label":"PROPFIND","value":14},{"label":"VIEW","value":15}]}
	 */
	public function getMethodLabel(Request $request)
	{
		$chapterApiLogic = new ChapterApiLogic();
		return generate_label('请求方式', $chapterApiLogic->getMethodLabel());
	}

	/**
	 * @api {get} /document/chapterapi/getEnabledLabel 获取必填类型列表
	 * @apiName getEnabledLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"label":"必填类型","option":[{"label":"False","value":1},{"label":"Ture","value":2}]}
	 */
	public function getEnabledLabel(Request $request)
	{
		$chapterApiParamLogic = new ChapterApiParamLogic();
		return generate_label('必填类型', $chapterApiParamLogic->getEnabledLabel());
	}

	/**
	 * @api {get} /document/chapterapi/getTypeLabel 获取字段类型列表
	 * @apiName getTypeLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"label":"字段类型","option":[{"label":"String","value":1},{"label":"Number","value":2},{"label":"Boolean","value":3},{"label":"Object","value":4},{"label":"Array","value":5},{"label":"Function","value":6},{"label":"RegExp","value":7},{"label":"Null","value":8}]}
	 */
	public function getTypeLabel(Request $request)
	{
		$chapterApiParamLogic = new ChapterApiParamLogic();
		return generate_label('字段类型', $chapterApiParamLogic->getTypeLabel());
	}

	/**
	 * @api {get} /document/chapterapi/getLocationLabel 获取请求类型列表
	 * @apiName getLocationLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"label":"请求类型","option":[{"label":"Request.Header","value":1},{"label":"Request.Query","value":2},{"label":"Request.Body.form-data","value":3},{"label":"Request.Body.urlencoded","value":4},{"label":"Request.Body.raw","value":5},{"label":"Request.Body.binary","value":6},{"label":"Reponse.Header","value":7},{"label":"Reponse.Body.form-data","value":8},{"label":"Reponse.Body.urlencoded","value":9},{"label":"Reponse.Body.raw","value":10},{"label":"Reponse.Body.binary","value":11}]}
	 */
	public function getLocationLabel(Request $request)
	{
		$chapterApiParamLogic = new ChapterApiParamLogic();
		return generate_label('请求类型', $chapterApiParamLogic->getLocationLabel());
	}

	/**
	 * @api {get} /document/chapterapi/rawContentType 获取RAW请求头列表
	 * @apiName rawContentType
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"label":"请求类型","option":[{"label":"Request.Header","value":1},{"label":"Request.Query","value":2},{"label":"Request.Body.form-data","value":3},{"label":"Request.Body.urlencoded","value":4},{"label":"Request.Body.raw","value":5},{"label":"Request.Body.binary","value":6},{"label":"Reponse.Header","value":7},{"label":"Reponse.Body.form-data","value":8},{"label":"Reponse.Body.urlencoded","value":9},{"label":"Reponse.Body.raw","value":10},{"label":"Reponse.Body.binary","value":11}]}
	 */
	public function rawContentType(Request $request)
	{
		$chapterApiParamLogic = new ChapterApiParamLogic();
		return generate_label('RAW请求头', $chapterApiParamLogic->rawContentType());
	}
}
