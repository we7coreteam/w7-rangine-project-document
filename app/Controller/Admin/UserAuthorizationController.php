<?php
namespace W7\App\Controller\Admin;

use W7\App\Model\Entity\Document;
use W7\App\Model\Logic\UserAuthorizationLogic;
use W7\Http\Message\Server\Request;

class UserAuthorizationController extends Controller
{
    public function __construct()
    {
        $this->logic = new UserAuthorizationLogic();
    }

    public function inviteUser(Request $request)
    {
		try{

			$this->validate($request, [
				'document_id' => 'required|integer|min:1',
				'user_id' => 'required|integer|min:1',
			], [
				'document_id.required' => '文档id必填',
				'document_id.min' => '文档id非法',
				'user_id.required' => '用户id必填',
				'user_id.min' => '用户id非法',
			]);
			$user_id = $request->input('user_id');
			$document_id = $request->input('document_id');

			$document = Document::find($document_id);
			if(!$document){
				return $this->error('该文档不存在');
			}
			if($request->document_user_auth !== APP_AUTH_ALL && $document->creator_id != $request->document_user_id){
				return $this->error('无权邀请');
			}

			$result = $this->logic->inviteUser($user_id, $document_id);

			return $this->success($result, '邀请成功');
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
    }

    public function leaveDocument(Request $request)
    {
	    try{
		    $this->validate($request, [
			    'document_id' => 'required|integer|min:1',
			    'user_id' => 'required|integer|min:1',
		    ], [
			    'document_id.required' => '文档id必填',
			    'document_id.min' => '文档id非法',
			    'user_id.required' => '用户id必填',
			    'user_id.min' => '用户id非法',
		    ]);
		    $document_id = $request->input('document_id');
		    $user_id = $request->input('user_id');

		    $document = Document::find($document_id);
		    if(!$document){
			    return $this->error('该文档不存在');
		    }
		    if($request->document_user_auth !== APP_AUTH_ALL && $document->creator_id != $request->document_user_id){
			    return $this->error('无权删除');
		    }

		    if($document->creator_id == $user_id){
			    return $this->error('创建者无法离开文档');
		    }

		    $this->logic->leaveDocument($user_id, $document_id);

		    return $this->success([], '从文档中删除用户成功');
	    }catch (\Exception $e){
		    return $this->error($e->getMessage());
	    }
    }
}
