<?php


namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\HomepageSettingLogic;
use W7\Http\Message\Server\Request;
//首页设置
class HomepageSettingController extends BaseController
{
	private function check(Request $request)
	{
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}


	/**
	 * @api {get} /admin/home/get-set  获取首页设置数据
	 * @apiName get-set
	 * @apiGroup HomepageSetting
	 *
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"open_home":{"is_open":"0","url":"http:\/\/192.168.168.99"},"banner":{"images":[]},"title":{"title":""}},"message":"ok"}
	 */
    public function getHomePageSet(Request $request){
		$this->check($request);
        return $this->data(HomepageSettingLogic::instance()->getHomeSet());
	}



	/**
	 * @api {post} /admin/home/set-open 设置是否打开首页
	 * @apiName set-open
	 * @apiGroup HomepageSetting
	 *
	 *
	 * @apiParam {Number} is_open 是否开启（0：关闭 1：开启）
	 */
	public function setOpenHome(Request $request){
		$this->check($request);
		$params = $this->validate($request, [
			'is_open' => 'required|integer',
		], [
			'is_open.required' => '请选择是否开启',
		]);
		$params['url'] = base_url();
		HomepageSettingLogic::instance()->setOpenHome($params);
		return $this->data('success');
	}


	/**
	 * @api {post} /admin/home/set-banner 设置首页banner
	 * @apiName set-banner
	 * @apiGroup HomepageSetting
	 *
	 *
	 * @apiParam {Array} images  图片
	 */
	public function setHomeBanner(Request $request){
		$this->check($request);
		$images = $request->post('images');
		$images = array_filter($images);
		if (empty($images)){
			throw new ErrorHttpException('图片不能为空!');
		}
		HomepageSettingLogic::instance()->setHomeBanner($images);
		return $this->data('success');
	}


	/**
	 * @api {post} /admin/home/set-title 设置首页名称
	 * @apiName set-title
	 * @apiGroup HomepageSetting
	 *
	 *
	 * @apiParam {String} title  名称
	 */
	public function setHomeTtile(Request $request){
		$this->check($request);
		$params = $this->validate($request, [
			'title' => 'string|required',
		], [
			'title.required' => '名称不能为空',
		]);

		HomepageSettingLogic::instance()->setHomeTitle($params['title']);
		return $this->data('success');
	}


}
