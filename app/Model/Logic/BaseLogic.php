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

    //设置缓存
    public function set($key,$value,$ttl=36000)
    {
        $this->getCache()->set($this->generateKey($key),$value,$ttl);
    }

    //删除缓存
    public function delete($key)
    {
        $this->getCache()->delete($this->generateKey($key));
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

}
