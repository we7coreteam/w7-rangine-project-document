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
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\DocumentHomeLogic;
use W7\App\Model\Logic\DocumentSearchLogic;
use W7\App\Model\Logic\HomepageSettingLogic;
use W7\Http\Message\Server\Request;

class DocumentHomeController extends BaseController
{


	/**
	 * @api {get} /document/home 前端首页数据
	 * @apiName home
	 * @apiGroup Document.home
	 *
	 */
	public function getDocumentHome(Request $request){
		//获取配置信息
		$set = HomepageSettingLogic::instance()->getHomeSet();
	    if (!$set['open_home']['is_open']){
	    	throw new ErrorHttpException('首页已关闭');
		}
        //公告
	    $notice = DocumentHomeLogic::instance()->getDocumentNotice();
	    //首页类型一
		$typeList_I = DocumentHomeLogic::instance()->getDocumentTypeI();
		//首页类型二
		$typeList_II = DocumentHomeLogic::instance()->getDocumentTypeII();
		$data = [
			'set' => $set,
			'notice' => $notice,
			'middle_list' => $typeList_I,
			'bottom_list' => $typeList_II,
		];
		return $this->data($data);
	}


	/**
	 * @api {get} /document/home/check 检测首页是否开启
	 * @apiName check
	 * @apiGroup Document.home
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"is_open":1,"url":"http:\/\/192.168.168.99:80"},"message":"ok"}
	 */
	public function checkHome(){
		$set = HomepageSettingLogic::instance()->getOpenHome();
		return $this->data($set);
	}



	/**
	 * @api {post} /document/home/search 前端首页搜索接口
	 * @apiName  search
	 * @apiGroup Document.home
	 *
	 * @apiParam {String} keywords 关键词
	 * @apiParam {Number} page 页码
	 * @apiParam {Number} page_size 页数
	 */
	 public function search(Request $request){
		 $page = intval($request->input('page', 1));
		 $pageSize = intval($request->input('page_size', 10));
		 $keyword = $request->input('keywords','');
		 //记录搜索词
		 DocumentSearchLogic::instance()->addSearchHotWord($keyword);
		 //搜索列表
		 $data = DocumentHomeLogic::instance()->searchDocument($keyword,$page,$pageSize);
         return  $this->data($data);
	 }

	/**
	 * @api {get} /document/home/search-hot 获取搜索热词列表
	 * @apiName  search-hot
	 * @apiGroup Document.home
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":["7","666","111","22","kkk"],"message":"ok"}
	 */
	 public function getSearchHot(){
	 	$data = DocumentSearchLogic::instance()->getSearchHotList();
	 	return $this->data($data);
	 }


}
