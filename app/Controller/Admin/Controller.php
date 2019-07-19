<?php


namespace W7\App\Controller\Admin;

use W7\Core\Controller\ControllerAbstract;


class Controller extends ControllerAbstract {
    protected $logic = null;

    public function success($data,$message='ok',$code=200)
    {
        return [
            'status' => true,
            'code' => $code,
            'data' => $data,
            'message' => $message,
        ];
    }

    public function error($message='fail',$code=400,$data=[])
    {
        return [
            'status' => false,
            'code' => $code,
            'data' => $data,
            'message' => $message,
        ];
    }

}
