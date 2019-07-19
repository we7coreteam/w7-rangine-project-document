<?php


namespace W7\App\Controller\Admin;


use W7\Http\Message\Server\Request;

class UserController extends Controller {
    public function addUser(Request $request){
//        C9F8QdEBAUMBFJXB24D
//        $name = $request->input('name');

        $this->validate($request,[
            'name' => 'required',
            'password' => 'required'
        ],[
            'name.required' => '请输入用户姓名',
            'password.required' => '请输入用户密码'
        ]);

        $data = [
            'uid' => $request->input('name'),
            'username' => 'rangine'
        ];

        return $data;



    }


}