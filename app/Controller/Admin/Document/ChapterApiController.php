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
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\Document\ChapterApiLogic;
use W7\App\Model\Logic\Document\ChapterApiParamLogic;
use W7\App\Model\Service\Document\ChapterDemoService;
use W7\App\Model\Service\Document\ChapterRuleDemoService;
use W7\App\Model\Service\Document\ChapterChangeService;
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

	/**
	 * @api {post} /document/chapterapi/textToData json或者键值对转换成data
	 * @apiName textToData
	 * @apiGroup ChapterApi
	 *
	 * @apiParam {String} data 数据内容
	 * @apiParam {String} type 0自适应，1指定json,2指定数组，3键值对字符串。默认0
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"label":"RAW请求头","option":[{"label":"text\/plain","value":0},{"label":"application\/json","value":1},{"label":"application\/javascript","value":2},{"label":"application\/xml","value":3},{"label":"text\/xml","value":4},{"label":"text\/html","value":5}]}
	 */
	public function textToData(Request $request)
	{
		$data = $request->post('data');
		$type = $request->post('type', 0);
		$obj = new ChapterChangeService();
		$data = $obj->textToData($data, $type);
		return $data;
	}

	/**
	 * @api {post} /document/chapterapi/getChapterRuleDemo 单个文档请求或响应规则演示
	 * @apiName getChapterRuleDemo
	 * @apiGroup Chapter
	 *
	 * @apiParam {Number} document_id 章节ID
	 * @apiParam {Number} chapter_id 文档ID
	 * @apiParam {Number} location_type 演示请求类型1请求2响应
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * [{"name":"type","type":8,"description":"","enabled":1,"default_value":"","rule":""},{"name":"image","type":3,"description":"","enabled":1,"default_value":"images\/20\/01\/13\/TFKPAt8u0fx6XqkCLBwohBjJa9Id0NVaxc5ViKSq.png","rule":""},{"name":"buy_type","type":3,"description":"","enabled":1,"default_value":2,"rule":""},{"name":"buy_limit","type":8,"description":"","enabled":1,"default_value":"","rule":""},{"name":"shipping_required","type":8,"description":"","enabled":1,"default_value":"","rule":""},{"name":"option_values","type":4,"description":"","enabled":1,"default_value":"","rule":"","children":[]},{"name":"image_path","type":3,"description":"","enabled":1,"default_value":"\/\/cdn.w7.cc\/images\/20\/01\/13\/TFKPAt8u0fx6XqkCLBwohBjJa9Id0NVaxc5ViKSq.png","rule":""}]
	 */
	public function getChapterRuleDemo(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
			'location_type' => 'required|in:1,2',
		], [
			'chapter_id.required' => '文档id必填',
			'chapter_id.min' => '文档id最小为0',
			'document_id.required' => '文档id必填',
			'location_type.required' => '演示请求类型id必填',
		]);
		$user = $request->getAttribute('user');
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapter = ChapterLogic::instance()->getById(intval($request->post('chapter_id')));
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		$obj = new ChapterRuleDemoService($chapter->id);
		$data = $obj->getChapterDemo($request->post('location_type'));
		return $data;
	}

	/**
	 * @api {post} /document/chapterapi/getChapterDemo 单个文档请求或响应导出json或键值对
	 * @apiName getChapterDemo
	 * @apiGroup Chapter
	 *
	 * @apiParam {Number} document_id 章节ID
	 * @apiParam {Number} chapter_id 文档ID
	 * @apiParam {Number} location_type 演示请求类型1请求2响应
	 * @apiParam {Number} type 1json格式，2键值对字符串，3键值对数组
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"data":{"Access-Token":"","4444":"","a":"1","b":["1","1"],"c":{"a":"a","b":{"0":"b","d":["4","5"]}},"e":[{"a":"1","b":"2","c":"3","f":["1","2"],"d":{"a":"1","b":"2"}}]},"descriptionData":{"Access-Token":"这是response头部说明7","4444":"","a":"","b":["",""],"c":{"a":"","b":{"0":"","d":["",""]}},"e":[{"a":"","b":"","c":"","f":["",""],"d":{"a":"","b":""}}]}}
	 */
	public function getChapterDemo(Request $request)
	{
		$this->validate($request, [
			'chapter_id' => 'required|integer|min:1',
			'document_id' => 'required|integer',
			'location_type' => 'required|in:1,2',
			'type' => 'required|in:1,2,3',
		], [
			'chapter_id.required' => '文档id必填',
			'chapter_id.min' => '文档id最小为0',
			'document_id.required' => '文档id必填',
			'location_type.required' => '演示请求类型id必填',
		]);
		$user = $request->getAttribute('user');
		if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$chapter = ChapterLogic::instance()->getById(intval($request->post('chapter_id')));
		if (empty($chapter)) {
			throw new ErrorHttpException('章节不存在');
		}

		$obj = new ChapterDemoService($chapter->id);
		$data = $obj->getChapterDemo($request->post('location_type'), $request->post('type'));
		return $data;
	}
}
