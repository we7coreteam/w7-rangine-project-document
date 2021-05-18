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

namespace W7\App\Controller\Admin\Article;

use W7\App\Controller\BaseController;
use W7\Http\Message\Server\Request;

class ArticleCollectionController extends BaseController
{
	/**
	 * @api {get} admin/article/collection 收藏文章-获取收藏文章列表
	 * @apiName index
	 * @apiGroup articleCollectionAdmin
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
	 * @apiSuccess {Object} user 作者信息
	 * @apiSuccess {String} user.username 作者昵称
	 *
	 * @apiSuccessExample {json} Success-Response:
	 *{"status":true,"code":200,"data":{"current_page":1,"data":[{"id":14,"column_id":1,"tag_ids":["2"],"user_id":1,"title":"爱我中华","content":"<p><span style=\"color: rgb(51, 51, 51); font-family: arial; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">根据世卫组织最新实时统计数据，截至欧洲中部夏令时间4月22日18时02分（北京时间4月23日0时02分），全球累计新冠肺炎确诊病例143445675例，累计死亡病例3051736例。22日全球新冠肺炎确诊病例新增874381例，单日新增病例数量为疫情暴发以来最高，死亡病例新增14033例。<\/span><\/p>","comment_status":1,"is_reprint":1,"reprint_url":"http:\/\/www.baidu.com\/","home_thumbnail":1,"read_num":5,"praise_num":2,"collection_num":1,"status":1,"reason":"","created_at":"1619166507","updated_at":"1621335168","status_text":"审核通过","user":{"id":1,"username":"admin","avatar":"https:\/\/wikidev-1257227245.cos.ap-shanghai.myqcloud.com\/document\/AsU43YDSTlhxdSInHsEhcc0603ec6f3Y.jpg","remark":"root","is_ban":0,"group_id":1,"company":"宿州市微擎云计算有限公司","resume":"计算机四级阿萨德阿萨德阿撒","skill":"微擎开发者，dz开发者","address":"合肥","created_at":"1569409778","updated_at":"1621305078"},"pivot":{"user_id":1,"article_id":14}}],"first_page_url":"\/?page=1","from":1,"last_page":1,"last_page_url":"\/?page=1","next_page_url":null,"path":"\/","per_page":10,"prev_page_url":null,"to":1,"total":1},"message":"ok"}
	 */
	public function index(Request $request)
	{
		$page = $request->query('page', 1);
		$pageSize = intval($request->input('page_size', 10));
		$user = $request->getAttribute('user');
		$result = $user->articleCollections()->with('user')->paginate($pageSize, ['*'], 'page', $page);
		return $this->data($result);
	}
}
