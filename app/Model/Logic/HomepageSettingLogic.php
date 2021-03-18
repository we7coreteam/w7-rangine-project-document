<?php

namespace W7\App\Model\Logic;

use W7\App;
use W7\Core\Helper\Traiter\InstanceTraiter;
use W7\Http\Message\Server\Request;

class HomepageSettingLogic extends BaseLogic
{
	use InstanceTraiter;

	const HOME_OPEN_KEY = 'open_home';
	const HOME_BANNER_KEY = 'home_banner';
	const HOME_TITLE_KEY = 'home_title';


	/**
	 * 获取首页配置
	 */
	public function getHomeSet()
	{
		return [
			 'open_home' => $this->getOpenHome(),
			 'banner' => $this->getHomeBanner(),
			 'title' => $this->getHomeTitle()
		];
	}


	/**
	 * 设置是否开启首页
	 * @param array $config
	 */
	public function setOpenHome( array $config){
         $setting['is_open'] = intval($config['is_open']);
         $setting['url'] = $config['url'];
         SettingLogic::instance()->save(self::HOME_OPEN_KEY,$setting);
	}

	/**
	 * 设置首页 banner
	 * @param array $images
	 */
	public function setHomeBanner(array $images){
		SettingLogic::instance()->save(self::HOME_BANNER_KEY,$images);
	}

	/**
	 * 设置首页名称
	 * @param  String $name
	 */
	public function setHomeTitle($name){
		SettingLogic::instance()->save(self::HOME_TITLE_KEY,$name);
	}


	/**
	 * 获取首页是否开启配置
	 */
	public function getOpenHome()
	{
		$setting = SettingLogic::instance()->getByKey(self::HOME_OPEN_KEY);
		if (!$setting){
			return ['open_home' => 0,'url'=>base_url()];
		}
		return $setting->setting;
	}


	/**
	 * 获取首页 banner 图
	 */
	public function getHomeBanner(){
		$setting = SettingLogic::instance()->getByKey(self::HOME_BANNER_KEY);
		if (!$setting){
			return [];
		}
		return $setting->setting;
	}


	/**
	 * 获取首页名称
	 */
	public function getHomeTitle(){
		$setting = SettingLogic::instance()->getByKey(self::HOME_TITLE_KEY);
		if (!$setting){
			return '';
		}
		return $setting->setting;
	}



}
