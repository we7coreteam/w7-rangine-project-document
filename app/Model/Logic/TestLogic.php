<?php

namespace W7\App\Model\Logic;



use W7\App\Model\Entity\Test;

class TestLogic extends BaseLogic {
    public function addUser($name)
    {
        $user = Test::create(['name'=>$name]);
        $this->set('zuser',$user);
        return $this->get('zuser');
    }
}
