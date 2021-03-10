<?php
/**
 * WeEngine Document System
 *
 * (c) We7Team 2021 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */
namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\DocumentFeedback;
use W7\App\Model\Entity\Setting;
use W7\App\Model\Logic\DocumentFeedbackLogic;
use W7\Http\Message\Server\Request;

class FeedbackController extends BaseController
{

	/**
	 * @api {post} /admin/document/feedback-list 反馈建议列表
	 * @apiName feedback-list
	 * @apiGroup document.Feedback
	 *
	 *
	 * @apiParam {Number} document_id 文档ID
	 * @apiParam {Number} page 页码
	 * @apiParam {Number} page_size 页数
	 */
	  public function getList(Request $request){
	  	 //验证
		  $params = $this->validate($request, [
			  'document_id' => 'required|integer|min:1',
		  ], [
			  'document_id.required' => '文档id必填',
			  'document_id.integer' => '文档id非法',
		  ]);
		  $page = intval($request->post('page'));
		  $pageSize = intval($request->post('page_size'));
          //获取用户信息
		  $user = $request->getAttribute('user');
		  if (!$user->isManager) {
			  throw new ErrorHttpException('您没有权限管理该文档',[],Setting::ERROR_NO_POWER);
		  }
          //查询数据
		  $query = DocumentFeedback::query()->where('document_id', '=', $params['document_id'])->orderByDesc('created_at');
		  $list = $query->paginate($pageSize, ['id','user_id','document_id','type','created_at'], 'page', $page);
		  foreach ($list->items() as $i => $row) {
			  $result['data'][] = [
				  'id' => $row->id,
				  'user_id' => $row->user_id,
				  'document_id' => $row->document_id,
				  'type' => $row->type,
				  'type_name' => $row->type_name,
				  'created_at' => $row->created_at->toDateTimeString()
			  ];
		  }

		  $result['page_count'] = $list->lastPage();
		  $result['total'] = $list->total();
		  $result['page_current'] = $list->currentPage();

		  return $this->data($result);
	  }


	/**
	 * @api {post} /admin/document/feedback-detail 反馈建议详情
	 * @apiName feedback-detail
	 * @apiGroup document.Feedback
	 *
	 *
	 * @apiParam {Number} feed_id 反馈数据ID
	 * @apiParam {Number} document_id 文档ID
	 */
	  public function detail(Request $request){
		  //验证
		  $params = $this->validate($request, [
			  'feed_id' => 'required|integer|min:1',
			  'document_id' => 'required|integer|min:1',
		  ], [
			  'feed_id.required' => '反馈数据id必填',
			  'feed_id.integer' => '反馈数据id非法',
			  'document_id.required' => '文档id必填',
			  'document_id.integer' => '文档id非法',
		  ]);

		  //获取用户信息
		  $user = $request->getAttribute('user');
		  if (!$user->isManager) {
			  throw new ErrorHttpException('您没有权限管理该文档',[],Setting::ERROR_NO_POWER);
		  }

		  $detail = DocumentFeedbackLogic::instance()->getByFeedbackDetail($params['feed_id'],$params['document_id']);
		  if (!$detail){
		  	   throw new ErrorHttpException('反馈数据不存在');
		  }
          //更新状态
		  DocumentFeedbackLogic::instance()->setByFeedbackStatus($params['feed_id'],$params['document_id']);

		  $result = [
			  'id'=> $detail->id,
			  'type'=> $detail->type,
			  'type_name'=> $detail->type_name,
			  'document_id'=> $detail->document_id,
			  'content'=> $detail->content,
			  'images'=> $detail->images,
			  'created_at' => $detail->created_at->toDateTimeString()
		  ];

		  return $this->data($result);
	  }


}
