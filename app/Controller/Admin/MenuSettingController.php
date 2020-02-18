<?php


namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\MenuSettingLogic;
use W7\Http\Message\Server\Request;

class MenuSettingController extends BaseController
{
	private function check(Request $request)
	{
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('无权访问');
		}
		return true;
	}

	public function all(Request $request)
	{
		$this->check($request);

		return $this->data(MenuSettingLogic::instance()->all());
	}

	public function add(Request $request)
	{
		$this->check($request);
		$config = $this->validate($request, [
			'name' => 'required',
			'sort' => 'required',
			'url' => 'required'
		]);

		MenuSettingLogic::instance()->add($config);

		return $this->data('success');
	}

	public function getById(Request $request)
	{
		$this->check($request);
		$params = $this->validate($request, [
			'id' => 'required'
		]);

		try {
			return $this->data(MenuSettingLogic::instance()->getById($params['id']));
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function updateById(Request $request)
	{
		$this->check($request);
		$config = $this->validate($request, [
			'id' => 'required',
			'name' => 'required',
			'sort' => 'required',
			'url' => 'required'
		]);

		try {
			MenuSettingLogic::instance()->updateById($config['id'], $config);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		return $this->data('success');
	}

	public function deleteById(Request $request)
	{
		$this->check($request);
		$params = $this->validate($request, [
			'id' => 'required'
		]);

		MenuSettingLogic::instance()->deleteById($params['id']);

		return $this->data('success');
	}
}