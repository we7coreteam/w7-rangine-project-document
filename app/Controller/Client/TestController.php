<?php


namespace W7\App\Controller\Client;
use W7\Http\Message\Server\Request;


class TestController extends Controller{
    public function index(Request $request) {
        $name = $request->input('name');
        return $this->success('Hello '.$name);
    }
}