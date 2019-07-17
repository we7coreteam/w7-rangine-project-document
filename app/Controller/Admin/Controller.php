<?php


namespace W7\App\Controller\Admin;

use W7\Core\Controller\ControllerAbstract;


class Controller extends ControllerAbstract {
    protected $logic = null;

    public function success($data,$message='ok')
    {
        return [
            'status' => true,
            'data' => $data,
            'message' => $message,
        ];
    }

    public function error($message='fail')
    {
        return [
            'status' => false,
            'data' => null,
            'message' => $message,
        ];
    }

}
