<?php


namespace W7\App\Controller\Client;

use W7\Core\Controller\ControllerAbstract;


class Controller extends ControllerAbstract {
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