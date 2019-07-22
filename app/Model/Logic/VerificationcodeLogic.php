<?php


namespace W7\App\Model\Logic;


class VerificationcodeLogic extends BaseLogic
{
    public function addCode($id,$flight,$time)
    {
//        return 'add';
        return $this->set('code_'.$id,$flight,$time);
    }

    public function getCode($id)
    {
        return $this->get('code_'.$id);
    }
}