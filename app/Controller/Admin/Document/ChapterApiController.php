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
use W7\Http\Message\Server\Request;

class ChapterApiController extends BaseController
{
	/**
	 * @api {get} /document/chapterapi/getApiLabel 获取参数类型列表
	 *
	 * @apiName getApiLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiParam {Array} data
	 * @apiParam {Array} data.statusCode 状态码列表
	 * @apiParam {Array} data.methodLabel 请求方式列表
	 * @apiParam {Array} data.enabledLabel 必填类型列表
	 * @apiParam {Array} data.typeLabel 字段类型
	 * @apiParam {Array} data.locationLabel 参数类型
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"statusCode":[200,301,403,404,500,502,503,504],"methodLabel":{"label":"请求方式","option":[{"label":"GET","value":1},{"label":"POST","value":2},{"label":"PUT","value":3},{"label":"PATCH","value":4},{"label":"DELETE","value":5},{"label":"COPY","value":6},{"label":"HEAD","value":7},{"label":"PTIONS","value":8},{"label":"LINK","value":9},{"label":"UNLINK","value":10},{"label":"PURGE","value":11},{"label":"LOCK","value":12},{"label":"UNLOCK","value":13},{"label":"PROPFIND","value":14},{"label":"VIEW","value":15}]},"enabledLabel":{"label":"必填类型","option":[{"label":"False","value":1},{"label":"Ture","value":2}]},"typeLabel":{"label":"字段类型","option":[{"label":"String","value":1},{"label":"Number","value":2},{"label":"Boolean","value":3},{"label":"Object","value":4},{"label":"Array","value":5},{"label":"Function","value":6},{"label":"RegExp","value":7},{"label":"Null","value":8}]},"locationLabel":{"label":"参数类型","option":[{"label":"Request.Query.Path","value":12},{"label":"Request.Header","value":1},{"label":"Request.Query.String","value":2},{"label":"Request.Body.form-data","value":3},{"label":"Request.Body.urlencoded","value":4},{"label":"Request.Body.raw","value":5},{"label":"Request.Body.binary","value":6},{"label":"Reponse.Header","value":7},{"label":"Reponse.Body.form-data","value":8},{"label":"Reponse.Body.urlencoded","value":9},{"label":"Reponse.Body.raw","value":10},{"label":"Reponse.Body.binary","value":11}]}},"message":"ok"}
	 */
	public function getApiLabel(Request $request)
	{
		return $this->data(ChapterApiLogic::instance()->getApiLabel());
	}
}
