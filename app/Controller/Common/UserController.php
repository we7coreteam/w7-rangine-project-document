<?php

namespace W7\App\Controller\Common;

use W7\App\Controller\BaseController;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\MenuSettingLogic;
use W7\App\Model\Logic\UserLogic;
use W7\App\Model\Logic\UserOperateLogic;
use W7\Http\Message\Server\Request;

class UserController extends BaseController
{

	/**
	 * 用户详情
	 * @param Request $request
	 * @return array
	 */
	public function info(Request $request){
		$param = $this->validate($request,[
			'user_id' => 'required|integer'
		],[
			'user_id' => '用户id'
		]);
		$Logic = new UserLogic();
		$user = $Logic->getByUid($param['user_id'])->setHidden(['userpass']);
		return $this->data($user);
	}

	public function update(Request $request){
		$param = $this->validate($request,[
			'id'      => 'required|integer',
			'avatar'  => 'sometimes|required',
			'company' => 'sometimes|required',
			'resume'  => 'sometimes|required',
			'skill'   => 'sometimes|required',
			'address' => 'sometimes|required',
		],[
			'id'      => '用户id',
			'avatar'  => '头像',
			'company' => '公司和职称',
			'resume'  => '简介',
			'skill'   => '技能',
			'address' => '地址'
		]);
		$Logic = new UserLogic();
		$user = $Logic->updateUser($param);
		return $this->data($user);
	}

	/**
	 * 我的动态
	 */
	public function operate(Request $request){
		$param = $this->validate($request,[
			'user_id' => 'required|integer'
		],[
			'user_id' => '用户id'
		]);
		$page	= $request->input('page',1);
		$size 	= $request->input('page_size',20);
		$Logic	= new UserOperateLogic();
		$param['operate'] = [UserOperateLog::CREATE,UserOperateLog::COLLECT];
		return $this->data($Logic->lists($param,$page,$size));
	}

}
