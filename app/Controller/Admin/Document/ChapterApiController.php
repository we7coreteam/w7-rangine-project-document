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
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Logic\ChapterLogic;
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
		return ChapterApi::getStatusCode();
	}

	/**
	 * @api {get} /document/chapterapi/getMethodLabel 获取请求方式列表
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
	 * @api {get} /document/chapterapi/getEnabledLabel 获取必填类型列表
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

	/**
	 * @api {get} /document/chapterapi/getTypeLabel 获取字段类型列表
	 * @apiName getTypeLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * ["1":"String","2":"Number","3":"Boolean","4":"Object","5":"Array","6":"Function","7":"RegExp","8":"Null"]
	 */
	public function getTypeLabel(Request $request)
	{
		return ChapterApiParam::getTypeLabel();
	}

	/**
	 * @api {get} /document/chapterapi/getLocationLabel 获取字段请求类型列表
	 * @apiName getLocationLabel
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"1":"Request.Header","2":"Request.Query","3":"Request.Body.form-data","4":"Request.Body.urlencoded","5":"Request.Body.raw","6":"Request.Body.binary","7":"Reponse.Header","8":"Reponse.Body.form-data","9":"Reponse.Body.urlencoded","10":"Reponse.Body.raw","11":"Reponse.Body.binary"}
	 */
	public function getLocationLabel(Request $request)
	{
		return ChapterApiParam::getLocationLabel();
	}

	/**
	 * @api {get} /document/chapterapi/rawContentType 获取RAW请求头列表
	 * @apiName rawContentType
	 * @apiGroup ChapterApi
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * ["text\/plain","application\/json","application\/javascript","application\/xml","text\/xml","text\/html"]
	 */
	public function rawContentType(Request $request)
	{
		return ChapterApiParam::rawContentType();
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
	 * [{"name":"type","type":8,"description":"","enabled":1,"default_value":"","rule":""},{"name":"image","type":3,"description":"","enabled":1,"default_value":"images\/20\/01\/13\/TFKPAt8u0fx6XqkCLBwohBjJa9Id0NVaxc5ViKSq.png","rule":""},{"name":"buy_type","type":3,"description":"","enabled":1,"default_value":2,"rule":""},{"name":"buy_limit","type":8,"description":"","enabled":1,"default_value":"","rule":""},{"name":"shipping_required","type":8,"description":"","enabled":1,"default_value":"","rule":""},{"name":"option_values","type":4,"description":"","enabled":1,"default_value":"","rule":"","children":[]},{"name":"image_path","type":3,"description":"","enabled":1,"default_value":"\/\/cdn.w7.cc\/images\/20\/01\/13\/TFKPAt8u0fx6XqkCLBwohBjJa9Id0NVaxc5ViKSq.png","rule":""}]
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
	 * {"data":"Access-Token:\n4444:\na:1\nb[0]:1\nb[1]:1\nc[a]:a\nc[b][0]:b\nc[b][d][0]:4\nc[b][d][1]:5\ne[0][a]:1\ne[0][b]:2\ne[0][c]:3\ne[0][f][0]:1\ne[0][f][1]:2\ne[0][d][a]:1&&\ne[0][d][b]:2=="}
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
