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

namespace W7\App\Controller\Article;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\UserStatus;
use W7\App\Model\Logic\Article\ArticleColumnLogic;
use W7\App\Model\Logic\UserStatusLogic;
use W7\Http\Message\Server\Request;

class ArticleColumnController extends BaseController
{
	protected function block()
	{
		return new ArticleColumnLogic();
	}

	/**
	 * @api {post} /article/articleColumn/infoUser 栏目-获取用户专栏
	 * @apiName infoUser
	 * @apiGroup articleColumn
	 *
	 * @apiParam {Number} user_id 用户ID
	 *
	 * @apiSuccess {String} name 栏目名称
	 * @apiSuccess {Number} article_num 排序
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} subscribe_num 关注数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"user_id":2,"name":"栏目3","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618906453","updated_at":"1618907138"},"message":"ok"}
	 */
	public function infoUser(Request $request)
	{
		$data = $this->validate($request, [
			'user_id' => 'required|integer',
		], [
			'user_id' => '用户ID',
		]);
		$result = $this->block()->info($data['user_id']);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleColumn/tags 栏目-当前栏目标签列表(已审核文章)
	 * @apiName tags
	 * @apiGroup articleColumn
	 *
	 * @apiParam {Number} column_id 栏目ID
	 *
	 * @apiSuccess {Object} tag_config 标签信息
	 * @apiSuccess {String} tag_config.name 标签名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":[{"id":1,"tag_id":2,"article_id":1,"column_id":1,"created_at":"1619054514","updated_at":"1619054514","tag_config":{"id":2,"name":"标签2","sort":0,"status":1,"created_at":"1619054227","updated_at":"1619054227"}}],"message":"ok"}
	 */
	public function tags(Request $request)
	{
		$data = $this->validate($request, [
			'column_id' => 'required|integer',
		], [
			'column_id' => '专栏ID',
		]);
		$result = $this->block()->tags($data['column_id']);
		return $this->data($result);
	}

	/**
	 * @api {get} /article/articleColumn/infoMy 栏目-详情
	 * @apiName infoMy
	 * @apiGroup articleColumn
	 *
	 * @apiSuccess {String} name 栏目名称
	 * @apiSuccess {Number} article_num 排序
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} subscribe_num 关注数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"user_id":2,"name":"栏目3","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618906453","updated_at":"1618907138"},"message":"ok"}
	 */
	public function infoMy(Request $request)
	{
		$user = $request->getAttribute('user');
		$result = $this->block()->info($user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleColumn 栏目-新增
	 * @apiName store
	 * @apiGroup articleColumn
	 *
	 * @apiParam {String} name 栏目名称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":2,"user_id":2,"name":"栏目3","article_num":0,"read_num":0,"subscribe_num":0,"praise_num":0,"created_at":"1618906453","updated_at":"1618907138"},"message":"ok"}
	 */
	public function store(Request $request)
	{
		$data = $this->validate($request, [
			'name' => 'required|string',
		], [
			'name' => '专栏名称',
		]);

		$user = $request->getAttribute('user');
		$data['user_id'] = $user->id;
		$result = $this->block()->add($data);
		UserStatusLogic::instance()->createStatus($result, $user, UserStatus::CREATE_COLUMN);
		return $this->data($result);
	}

	/**
	 * @api {put} /article/articleColumn/:id 栏目-修改
	 * @apiName update
	 * @apiGroup articleColumn
	 *
	 * @apiParam {String} name 栏目名称
	 * @apiParam {String} avatar 栏目头像
	 *
	 * @apiSuccess {String} name 栏目名称
	 * @apiSuccess {String} avatar 栏目头像
	 * @apiSuccess {Number} article_num 排序
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} subscribe_num 关注数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":41,"user_id":201,"name":"222","avatar":"123","article_num":2,"read_num":15,"subscribe_num":2,"praise_num":0,"status":1,"created_at":"1621564718","updated_at":"1622439648"},"message":"ok"}
	 */
	public function update(Request $request, $id)
	{
		$data = $this->validate($request, [
			'name' => 'sometimes|string',
			'avatar' => 'sometimes|string',
		], [
			'name' => '专栏名称',
			'avatar' => '专栏头像',
		]);

		$user = $request->getAttribute('user');
		//必须本用户修改
		$checkData['user_id'] = $user->id;

		$result = $this->block()->save($id, $data, $checkData);
		return $this->data($result);
	}
}
