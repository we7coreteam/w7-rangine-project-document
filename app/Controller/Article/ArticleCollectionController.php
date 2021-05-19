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
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\Article\ArticleCollectionLogic;
use W7\Http\Message\Server\Request;

class ArticleCollectionController extends BaseController
{
	protected function block()
	{
		return new ArticleCollectionLogic();
	}

	/**
	 * @api {get} admin/article/collection 收藏文章-获取收藏文章列表
	 * @apiName index
	 * @apiGroup articleCollectionAdmin
     *
     * @apiParam {Number} user_id 用户id
	 *
	 * @apiSuccess {Number} column_id 栏目ID
	 * @apiSuccess {Array} tag_ids 标签列表
	 * @apiSuccess {Number} user_id 用户ID
	 * @apiSuccess {String} title 文章标题
	 * @apiSuccess {String} content 文章内容
	 * @apiSuccess {Number} comment_status 是否开启评论
	 * @apiSuccess {Number} is_reprint 文章来源
	 * @apiSuccess {Number} reprint_url 来源链接
	 * @apiSuccess {Number} home_thumbnail 首页缩略图
	 * @apiSuccess {Number} read_num 阅读数量
	 * @apiSuccess {Number} praise_num 点赞数量
	 * @apiSuccess {Number} status 状态0待审核1已审核2审核失败
	 * @apiSuccess {Number} reason 驳回描述
	 * @apiSuccess {String} time_str 格式化时间
	 * @apiSuccess {Object} user 作者信息
	 * @apiSuccess {String} user.username 作者昵称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"current_page":1,"data":[{"id":16,"column_id":1,"tag_ids":["4","5","6","7"],"user_id":1,"title":"小白兔白又白","content":"<p><span style=\"color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;; font-size: 14px; white-space: pre-wrap; background-color: rgb(255, 255, 255);\">小白兔白又白，两只耳朵竖起来，\n\n爱吃萝卜爱吃菜，蹦蹦跳跳真可爱。\n\n小白兔白又白，两只耳朵竖起来，\n\n爱吃萝卜爱吃菜，蹦蹦跳跳真可爱<\/span><\/p>","comment_status":1,"is_reprint":0,"reprint_url":"","home_thumbnail":1,"read_num":4,"praise_num":1,"collection_num":1,"status":1,"reason":"","created_at":"1619321936","updated_at":"1621333349","time_str":"2021-04-25 11:38","status_text":"审核通过","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/AsU43YDSTlhxdSInHsEhcc0603ec6f3Y.jpg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级阿萨德阿萨德阿撒","skill":"微擎开发者，dz开发者.","address":"合肥","created_at":"1569409778","updated_at":"1621402909"},"pivot":{"user_id":1,"article_id":16}}],"first_page_url":"\/?page=1","from":1,"last_page":1,"last_page_url":"\/?page=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
        $param = $this->validate($request,[
            'user_id' => 'required|integer'
        ],[
            'user_id' => '用户id'
        ]);
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$user = User::find($param['user_id']);
		$result = $user->articleCollections()->where('article_collection.status', 1)->with('user')->paginate($pageSize, ['*'], 'page', $page);
		return $this->data($result);
	}

	/**
	 * @api {get} /article/articleCollection/info 收藏文章-获取当前文章是否收藏
	 * @apiName info
	 * @apiGroup articleCollection
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已收藏0未收藏
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":11,"article_id":14,"user_id":1,"status":1,"created_at":"1621326274","updated_at":"1621331752"},"message":"ok"}
	 */
	public function info(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->info($data['article_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleCollection/collection 收藏文章-收藏
	 * @apiName collection
	 * @apiGroup articleCollection
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 1已收藏
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":11,"article_id":14,"user_id":1,"status":1,"created_at":"1621326274","updated_at":"1621335136"},"message":"ok"}
	 */
	public function collection(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->collection($data['article_id'], $user->id);
		return $this->data($result);
	}

	/**
	 * @api {post} /article/articleCollection/unCollection 收藏文章-取消收藏
	 * @apiName unCollection
	 * @apiGroup articleCollection
	 *
	 * @apiParam {Number} article_id 文章ID
	 *
	 * @apiSuccess {Number} status 0未收藏
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":11,"article_id":14,"user_id":1,"status":0,"created_at":"1621326274","updated_at":"1621335168"},"message":"ok"}
	 */
	public function unCollection(Request $request)
	{
		$data = $this->validate($request, [
			'article_id' => 'required|integer',
		], [
			'article_id' => '文章ID',
		]);
		$user = $request->getAttribute('user');
		$result = $this->block()->unCollection($data['article_id'], $user->id);
		return $this->data($result);
	}
}
