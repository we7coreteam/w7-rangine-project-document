<?php

namespace W7\App\Model\Logic;



use W7\App\Model\Entity\Test;
use W7\App\Model\Service\Api\TestApi;

class TestLogic extends BaseLogic
{
    public function addUser($name)
    {
        $user = Test::create(['name'=>$name]);
        return $user;
    }

    public function getUser($id)
    {
        $testapi = new TestApi();
        return $testapi->getTraceid();
        $cacheUser = $this->get('user_'.$id);
        if($cacheUser){
            $user = $cacheUser;
            $user->from = 'cache';
        }else{
            $user = Test::find($id);
            $this->set('user_'.$id,$user);
        }
        return $user;
    }
}
