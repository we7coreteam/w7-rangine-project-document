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
//        return $request->document_user_id;
        try{
            $page = $request->input('page');
            $result = $this->logic->getDocuments($page);
            return $this->success($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try{
            $this->validate($request, [
                'name' => 'string|required|max:30',
                'sort' => 'integer|min:0',
                'content' => 'required'
            ],[
                'name.required' => '文档名称必填',
                'name.max' => '文档名最大３０个字符',
                'sort.min' => '排序最小值为０',
                'content.required' => '文档内容必填'
            ]);

            $data['creator_id'] = 1;
            $data['name'] = $request->input('name');
            $data['icon'] = $request->input('icon','');
            $content = $request->input('content');
            $data['is_show'] = $request->input('is_show',1);
            $data['sort'] = $request->input('sort',0);

            $result = $this->logic->createDocument($data,$content);
            if($result){
                return $this->success($result);
            }
            return $this->error($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}
