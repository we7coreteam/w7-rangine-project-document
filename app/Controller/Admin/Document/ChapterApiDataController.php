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
use W7\App\Model\Entity\Document\ChapterApiData;
use W7\App\Model\Entity\Setting;
use W7\App\Model\Logic\Document\ChapterApiDataLogic;
use W7\Http\Message\Server\Request;

class ChapterApiDataController extends BaseController
{

	/**
	 * @api {post} /admin/document/chapterapi/setApiData  API请求数据保存
	 * @apiName setApiData
	 * @apiGroup Chapterapi
	 *
	 * @apiParam {Number} chapter_id 章节ID
	 * @apiParam {Number} document_id 文档ID
	 * @apiParam {Number} respond_id  数据ID 非必填 编辑的时候传参
	 * @apiParam {String} respond 数据文本
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {status: true, code: 200, data: "success", message: "ok"}
	 */
	public function setData(Request $request){
		//验证
		 $params = $this->validate($request,[
			 'respond' => 'string|required',
			 'chapter_id' => 'required|integer|min:1',
			 'document_id' => 'required|integer',
		 ],[
			 'respond.required' => '章节名称必填',
			 'chapter_id.required' => '文档id必填',
			 'chapter_id.min' => '文档id最小为0',
			 'document_id.required' => '文档id必填',
		 ]);

		 $user = $request->getAttribute('user');
		 if (!$user->isOperator) {
			throw new ErrorHttpException('您没有权限管理该文档', [], Setting::ERROR_NO_POWER);
		 }

		 $respondId = intval($request->post('respond_id',0));
		 if ($respondId){
			 //先删除原先的数据
			 $chapter = ChapterApiDataLogic::instance()->deleteChapterApiData($params['chapter_id']);
			 if (!$chapter) {
				 throw new ErrorHttpException('数据更新错误');
			 }
		 }

		 ChapterApiData::query()->create([
		 	     'chapter_id'=>$params['chapter_id'],
		 	     'respond'=> $params['respond']
		 ]);

		 return $this->data('success');
	}


	/**
	 * @api {get} /admin/document/chapterapi/getData  API请求数据保存
	 * @apiName getData
	 * @apiGroup Chapterapi
	 *
	 * @apiParam {Number} chapter_id 章节ID
	 */
	public function getData(Request $request){
		//验证
		$params = $this->validate($request,[
			'chapter_id' => 'required|integer|min:1',
		],[
			'chapter_id.required' => '文档id必填',
			'chapter_id.min' => '文档id最小为0',
		]);

        $chapter = ChapterApiDataLogic::instance()->getByChapterApiData($params['chapter_id']);
        if (!$chapter){
			throw new ErrorHttpException('获取数据失败');
		}

        return $this->data($chapter);

	}



}
