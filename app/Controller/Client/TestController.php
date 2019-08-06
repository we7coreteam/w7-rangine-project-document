<?php


namespace W7\App\Controller\Client;
use Illuminate\Cache\CacheManager;
use W7\App\Model\Logic\TestLogic;
use W7\App\Model\Service\Session\Manager;
use W7\App\Model\Service\SessionLogic;
use W7\Http\Message\Server\Request;


class TestController extends Controller{
    public function __construct()
    {
        $this->logic = new TestLogic();
    }

    public function index(Request $request) {

        try{
        	return $this->success(session('aaa',null));
	        cache()->set('test','你好，世界 + hello world = PHP',5);
	        return $this->success(cache()->get('test'));
//            $this->validate($request, [
//                'name' => 'required|max:255',
//                'id' => 'required',
//            ],[
//                'id.required' => 'id必填',
//            ]);
//
//            $name = $request->input('name');
//            $id = $request->input('id');
//            $res = $this->logic->addUser($name);
            $res = $this->logic->getUser(0);
            if($res){
                return $this->success($res);
            }
            return $this->error('用户不存在');
        }catch (\Exception $e){
            return $this->error($e->getMessage(),400,[$e->getFile(),$e->getLine()]);
        }
    }
}
