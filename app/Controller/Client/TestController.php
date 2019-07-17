<?php


namespace W7\App\Controller\Client;
use W7\App\Model\Logic\TestLogic;
use W7\Http\Message\Server\Request;


class TestController extends Controller{
    public function __construct()
    {
        $this->logic = new TestLogic();
    }

    public function index(Request $request) {
        try{
            $this->validate($request, [
                'name' => 'required|max:255',
                'id' => 'required',
            ],[
                'id.required' => 'id必填',
            ]);

            $name = $request->input('name');
            $id = $request->input('id');
//            $res = $this->logic->addUser($name);
            $res = $this->logic->getUser($id);
            if($res){
                return $this->success($res);
            }
            return $this->error('用户不存在');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}
