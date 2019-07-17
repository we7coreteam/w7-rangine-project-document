<?php
namespace W7\App\Model\Service\Api;


class TestApi extends ApiBase
{
    protected $domain = 'http://www.baidu.com';

    public function getTraceid()
    {
        return $this->get('');
    }
}
