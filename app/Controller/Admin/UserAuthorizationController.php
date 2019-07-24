<?php
namespace W7\App\Controller\Admin;

use W7\App\Model\Logic\UserAuthorizationLogic;
use W7\Http\Message\Server\Request;

class UserAuthorizationController extends Controller
{
    public function __construct()
    {
        $this->logic = new UserAuthorizationLogic();
    }

    public function index(Request $request)
    {
        try {
            if (APP_AUTH_ALL !== $request->document_user_auth) {
                return $this->error('无权访问');
            }
            $user_id = $request->input('user_id');
//            $result = $this->logic->getItems($user_id);
	        $result = $this->logic->getAuthByCategory($user_id);
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        //auth = {"document":[{"id":0},{"id":1,"can_read":1,"can_modify":0,"can_delete":0},{"id":2,"can_read":1,"can_modify":1,"can_delete":1}]}
        try {
            if (APP_AUTH_ALL !== $request->document_user_auth) {
                return $this->error('无权操作');
            }
            $auth = json_decode($request->input('auth', '{}'), 1);
            $user_id = $request->input('user_id');
            if (!$user_id) {
                return $this->error('缺少参数');
            }
            $result = $this->logic->updateAuth($user_id, $auth);

            return $this->success($result, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
