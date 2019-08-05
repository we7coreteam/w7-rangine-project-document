<?php

namespace W7\App\Model\Logic;


use W7\Core\Cache\Cache;
use W7\Core\Database\LogicAbstract;

class BaseLogic extends LogicAbstract {
    private $cache = null;
    private $prefix = 'document_logic_';


    //获取缓存
    public function get($key,$default=null)
    {
        return $this->getCache()->get($this->generateKey($key),$default);
    }

    public function increment($key,$ttl=24*3600,$step=1)
    {
    	$value = $this->get($key);
    	if($value){
    		$value = intval($value) + intval($step);
    		$this->set($key,$value);
	    }else{
		    $this->set($key,1,$ttl);
	    }
    	return true;
    }

	public function decrement($key,$ttl,$step=1)
	{
		$value = $this->get($key);
		if($value){
			$value = intval($value) - intval($step);
			$this->set($key,$value);
			return true;
		}
		return false;
	}

    //设置缓存
    public function set($key,$value,$ttl=24*3600)
    {
        return $this->getCache()->set($this->generateKey($key),$value,$ttl);
    }

    //删除缓存
    public function delete($key)
    {
        return $this->getCache()->delete($this->generateKey($key));
    }

    public function getCache()
    {
        if(!$this->cache) {
            $this->cache = new Cache();
        }
        return $this->cache;
    }

    public function generateKey($key)
    {
        return $this->prefix.$key;
    }

    public function checkRepeatRequest($user_id,$ttl=2)
    {
        if($this->get('repeat_'.$user_id)){
            throw new \Exception('重复请求，请稍后再试');
        }
        $this->set('repeat_'.$user_id,1,$ttl);
    }

//    public function checkWindControl($user_id,$key)
//    {
//	    $max = WindControlConfig::get($key);//max_number_added_per_day
//	    if($this->get($key.'_'.date('Ymd').'_'.$user_id,0) >= $max){
//	    	//report wind control
//		    $report = WindControlReport::where('operator_id',$user_id)
//			    ->where('config_id',$key)
//			    ->where('created_at','>',strtotime(date('Y-m-d 00:00:00')))
//			    ->first();
//		    if(!$report){
//			    WindControlReport::create(['config_id'=>$key,'detail'=>WindControlConfig::$errors[$key].$max,'operator_id'=>$user_id]);
//		    }
//		    throw new \Exception(WindControlConfig::$errors[$key].$max);
//	    }
//    }

}
