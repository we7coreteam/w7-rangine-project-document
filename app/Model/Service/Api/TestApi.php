<?php
namespace W7\App\Model\Service\Api;


class TestApi extends Curl
{
    protected $baseUrl = 'www.baidu.com';

    public function getChapter($data=[])
    {
    	$this->responseType = 'html';
        dd($this->get('admin/chapter/index',$data));
    }
}
