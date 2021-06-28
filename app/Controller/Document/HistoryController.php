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

namespace W7\App\Controller\Document;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Document\HistoryLogic;
use W7\Http\Message\Server\Request;
use W7\App\Exception\ErrorHttpException;

class HistoryController extends BaseController
{
	protected function block()
	{
		return new HistoryLogic();
	}

	/**
	 * @api {get} document/history/all 文档历史版本-版本列表
	 * @apiName all
	 * @apiGroup history
	 *
	 * @apiParam {Number} document_id 文档id
	 *
	 * @apiSuccess {Number} id 历史版本id
	 * @apiSuccess {Number} document_id 文档id
	 * @apiSuccess {String} name 文档名称
	 * @apiSuccess {String} created_at 更新时间
	 * @apiSuccess {String} user.username 编辑人名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"current_page":1,"data":[{"id":7,"document_id":1298,"name":"123","creator_id":1,"created_at":"2021-06-17 18:16:26","updated_at":"1623924986","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/AsU43YDSTlhxdSInHsEhcc0603ec6f3Y.jpg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1623750351","follower_num":11,"following_num":11,"article_num":42}},{"id":6,"document_id":1298,"name":"123","creator_id":1,"created_at":"2021-06-17 18:12:53","updated_at":"1623924773","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/AsU43YDSTlhxdSInHsEhcc0603ec6f3Y.jpg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1623750351","follower_num":11,"following_num":11,"article_num":42}},{"id":5,"document_id":1298,"name":"123","creator_id":1,"created_at":"2021-06-17 18:09:08","updated_at":"1623924548","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/AsU43YDSTlhxdSInHsEhcc0603ec6f3Y.jpg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级","skill":"微擎官方账号","address":"合肥","created_at":"1569409778","updated_at":"1623750351","follower_num":11,"following_num":11,"article_num":42}}],"first_page_url":"\/?=1","from":1,"last_page":1,"last_page_url":"\/?=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":3,"total":3},"message":"ok"}∂
	 */
	public function all(Request $request)
	{
		$page = intval($request->input('page', 1));
		$pageSize = intval($request->input('page_size', 10));
		$params = $this->validate($request, [
			'document_id' => 'integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法'
		]);
		$condition = [
			['document_id', '=', $params['document_id']]
		];
		$list = $this->block()->index($condition, $page, $pageSize, ['user']);
		return $this->data($list);
	}

	/**
	 * @api {get} document/history/detail 文档历史版本-文档详情
	 * @apiName detail
	 * @apiGroup history
	 *
	 * @apiParam {Number} document_id 文档id
	 * @apiParam {Number} history_id 版本id
	 *
	 * @apiSuccess {Number} id 历史版本id
	 * @apiSuccess {Number} document_id 文档id
	 * @apiSuccess {String} name 文档名称
	 * @apiSuccess {String} created_at 更新时间
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":7,"document_id":1298,"name":"123","creator_id":1,"created_at":"2021-06-17 18:16:26","updated_at":"1623924986"},"message":"ok"}
	 */
	public function detail(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
			'history_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法',
			'history_id.required' => '历史版本id必填',
			'history_id.integer' => '历史版本id非法'
		]);

		$info = $this->block()->getById($params['history_id'], $params['document_id']);
		if (!$info) {
			throw new ErrorHttpException('此记录不存在');
		}
		return $this->data($info->toArray());
	}
}
