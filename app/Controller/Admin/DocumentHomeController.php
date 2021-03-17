<?php


namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\DocumentHomeLogic;
use W7\Http\Message\Server\Request;
//首页文档设置
class DocumentHomeController extends BaseController
{
	protected  $_user;

	private function check(Request $request)
	{
		$this->_user= $request->getAttribute('user');
		if (!$this->_user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}


	/**
	 * @api {get} /admin/home/list 首页文档设置列表
	 *
	 * @apiName list
	 * @apiGroup DocumentHome
	 *
	 * @apiParam {Number} type 类型 (0:全部 1：公告 2：首页类型一 3：首页类型二)
	 *
	 */
	public function getList(Request $request){
		$this->check($request);
		$params = $this->validate($request, [
			'type' => 'required|integer',
		], [
			'type.required' => '请选择类型',
		]);
		$page = intval($request->post('page'));
		$pageSize = intval($request->post('page_size'));
		$data = DocumentHomeLogic::instance()->getListData($params['type'],$page,$pageSize);
		return $this->data($data);
	}


	/**
	 * @api {post} /admin/home/add 首页文档设置-添加
	 *
	 * @apiName add
	 * @apiGroup DocumentHome
	 *
	 * @apiParam {Number} type 类型 ( 1：公告 2：首页类型一 3：首页类型二)
	 * @apiParam {Number} document_id 文档id
	 * @apiParam {String} logo 文档图标（类型 2 必填，其他非必填）
	 * @apiParam {String} desc 文档简介（非必填）
	 * @apiParam {Number} sort 排序值（非必填）
	 *
	 */
	public function addHomeData(Request $request){
		$this->check($request);
		$params = $this->validate($request, [
			'type' => 'required|integer|min:1',
			'document_id' => 'required|integer',
		], [
			'type.required' => '请选择类型',
			'type.min' => '类型最小为 1',
			'document_id.required' => '请选择文档',
		]);
		//不同类型参数判断
		if (intval($params['type']) == 2){ //首页类型一
			 $logo = $request->post('logo');
			 if (!$logo){
			 	throw new ErrorHttpException('请上传图标');
			 }
			 $params['logo'] = $logo;
			 $params['description'] = htmlspecialchars(trim($request->post('desc')),ENT_QUOTES);
		}
		$params['user_id'] = $this->_user->id;
        //排序
		$params['sort'] = intval($request->post('sort',0));
		DocumentHomeLogic::instance()->addHomeData($params);
        return $this->data('success');
	}


	/**
	 * @api {all} /admin/home/edit 首页文档设置-编辑
	 *
	 * @apiName edit
	 * @apiGroup DocumentHome
	 *
	 * @apiParam {Number} id  主键 ID （ GET请求只传 id  GET 获取数据  POST 提交数据）
	 * @apiParam {Number} type 类型 ( 1：公告 2：首页类型一 3：首页类型二)
	 * @apiParam {Number} document_id 文档id
	 * @apiParam {String} logo 文档图标（类型 2 必填，其他非必填）
	 * @apiParam {String} desc 文档简介（非必填）
	 * @apiParam {Number} sort 排序值（非必填）
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"id":24,"user_id":1,"user":"admin","type":2,"type_name":"首页类型一","document_id":2,"document_name":"开源文档","url":"","description":"fgfdhfgh","sort":11,"created_at":"2021-03-16 17:04:52"},"message":"ok"}
	 */
	public function editHomeData(Request $request){
		$this->check($request);
		//get 请求
		if ($request->getMethod() == 'GET'){
			$params = $this->validate($request, [
				'id' => 'required|integer|min:1',
			], [
				'id.required' => 'id 不能为空',
				'id.min' => 'id 最小为 1',
			]);
			$data = DocumentHomeLogic::instance()->getByHomeData($params['id']);
			return  $this->data($data);
		//POST 请求
		}elseif ($request->getMethod() == 'POST'){
			$params = $this->validate($request, [
				'id' => 'required|integer|min:1',
				'type' => 'required|integer|min:1',
				'document_id' => 'required|integer',
			], [
				'id.required' => 'id 不能为空',
				'id.min' => 'id 最小为 1',
				'type.required' => '请选择类型',
				'type.min' => '类型最小为 1',
				'document_id.required' => '请选择文档',
			]);
			//不同类型参数判断
			if (intval($params['type']) == 2){ //首页类型一
				$logo = $request->post('logo');
				if (!$logo){
					throw new ErrorHttpException('请上传图标');
				}
				$params['logo'] = $logo;
				$params['description'] = htmlspecialchars(trim($request->post('desc')),ENT_QUOTES);
			}
			$params['user_id'] = $this->_user->id;
			//排序
			$params['sort'] = intval($request->post('sort',0));
			DocumentHomeLogic::instance()->editHomeData($params);
			return $this->data('success');
		}

		return $this->data('非法请求');
	}


	/**
	 * @api {post} /admin/home/delete 首页文档设置-删除操作
	 *
	 * @apiName delete
	 * @apiGroup DocumentHome
	 *
	 * @apiParam {Number} id  主键 ID
	 *
	 */
	public function delHomeData(Request $request){
		$this->check($request);
		$params = $this->validate($request, [
			'id' => 'required|integer|min:1',
		], [
			'id.required' => 'id 不能为空',
			'id.min' => 'id 最小为 1',
		]);

		DocumentHomeLogic::instance()->delHomeData(intval($params['id']));

		return $this->data('success');
	}


	/**
	 * @api {get} /admin/home/get-type 首页文档设置-获取类型
	 *
	 * @apiName get-type
	 * @apiGroup DocumentHome
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"1":"公告","2":"首页类型一","3":"首页类型二"},"message":"ok"}
	 */
	public function getType(Request $request){
		$this->check($request);
		return $this->data(DocumentHomeLogic::instance()->getTypeData());
	}


	/**
	 * @api {post} /admin/home/search-doc 首页文档设置-模糊查询文档
	 *
	 * @apiName search-doc
	 * @apiGroup DocumentHome
	 *
	 * @apiParam {String} keyword 关键词（非必填）
	 *
	 */
	public function queryDocument(Request $request){
		$this->check($request);
        $keyword = trim($request->post('keyword'));
        $data = DocumentHomeLogic::instance()->queryDocument($keyword,1);
        return $this->data($data);
	}



}
