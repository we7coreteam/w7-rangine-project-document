<?php
namespace W7\App\Controller\Admin;

use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->logic = new DocumentLogic();
    }

    public function index(Request $request)
    {
        try{
            $auth = $request->document_user_auth;

            if($auth === APP_AUTH_ALL){
                $allow_ids = [];
            }else{
                $allow_ids = [0];
                foreach($auth['document'] as $document){
                    if($document['can_read']){
                        $allow_ids[] = $document['function_id'];
                    }
                }
            }

            $page = $request->input('page',1);
            $category = $request->input('category_id',0);
            $size = $request->input('size',10);
            $result = $this->logic->getDocuments($page,$size,$category,$allow_ids);
            return $this->success($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try{
            $auth = $request->document_user_auth;

            if($auth !== APP_AUTH_ALL){
                if(!isset($auth['document'][0])){
                    return $this->error('没有创建文档的权限!');
                }
            }

            $this->logic->checkRepeatRequest($request->document_user_id);

            $this->validate($request, [
                'name' => 'string|required|max:30',
                'sort' => 'integer|min:0',
                'category_id' => 'integer|min:1',
                'content' => 'required'
            ],[
                'name.required' => '文档名称必填',
                'name.max' => '文档名最大３０个字符',
                'sort.min' => '排序最小值为０',
                'content.required' => '文档内容必填',
                'category_id.required' => '分类id必填',
            ]);

            $data['creator_id'] = $request->document_user_id;
            $data['name'] = $request->input('name');
            $data['icon'] = $request->input('icon','');
            $content = $request->input('content');
            $data['is_show'] = $request->input('is_show',1);
            $data['sort'] = $request->input('sort',0);
            $data['category_id'] = $request->input('category_id');

            $result = $this->logic->createDocument($data,$content);
            if($result){
                return $this->success($result);
            }
            return $this->error($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try{
            $auth = $request->document_user_auth;
            $id = $request->input('id');
            if(!$id){
                return $this->error('id必传');
            }

            if($auth !== APP_AUTH_ALL){
                if(!isset($auth['document'][$id]) || $auth['document'][$id]['can_modify'] == 0){
                    return $this->error('没有修改该文档的权限!');
                }
            }

            $this->logic->checkRepeatRequest($request->document_user_id);

            $this->validate($request, [
                'name' => 'string|required|max:30',
                'sort' => 'integer|min:0',
                'category_id' => 'integer|min:1',
                'content' => 'required'
            ],[
                'name.required' => '文档名称必填',
                'name.max' => '文档名最大３０个字符',
                'sort.min' => '排序最小值为０',
                'content.required' => '文档内容必填',
                'category_id.required' => '分类id必填',
            ]);

            $data['creator_id'] = $request->document_user_id;
            $data['name'] = $request->input('name');
            $data['icon'] = $request->input('icon','');
            $content = $request->input('content');
            $data['is_show'] = $request->input('is_show',1);
            $data['sort'] = $request->input('sort',0);
            $data['category_id'] = $request->input('category_id');

            $result = $this->logic->updateDocument($id,$data,$content);
            if($result){
                return $this->success($result);
            }
            return $this->error($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function show(Request $request)
    {
        try{
            $auth = $request->document_user_auth;
            $id = $request->input('id');
            if(!$id){
                return $this->error('id必传');
            }
            if($auth !== APP_AUTH_ALL){
                if(!isset($auth['document'][$id]) || $auth['document'][$id]['can_read'] == 0){
                    return $this->error('该文档不存在!');
                }
            }
            $result = $this->logic->getDocument($id);
            if($result){
                return $this->success($result);
            }
            return $this->error($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
    	try{
			$id = $request->input('id');
			$auth = $request->document_user_auth;
			if(!$id){
				return $this->error('id必传');
			}
			if($auth !== APP_AUTH_ALL){
				if(!isset($auth['document'][$id]) || $auth['document'][$id]['can_delete'] == 0){
					return $this->error('没有删除该文档的权限!!');
				}
			}
			$this->logic->deleteDocument($id);
			return $this->success();
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
    }


}
