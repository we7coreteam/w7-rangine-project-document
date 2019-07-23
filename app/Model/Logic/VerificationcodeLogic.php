<?php


namespace W7\App\Model\Logic;


class VerificationcodeLogic extends BaseLogic
{
    public function addCode($key,$flight,$time)
    {
        return $this->set($key,$flight,$time);
    }

    public function getCode($key)
    {
        return $this->get($key);
    }
}