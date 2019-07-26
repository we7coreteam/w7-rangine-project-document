<?php
namespace W7\App\Controller\Admin;

use W7\App\Model\Logic\ChapterLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->logic = new ChapterLogic();
    }

    public function index(Request $request)
    {
        try {
//            $auth = $request->document_user_auth;
//
//            if (APP_AUTH_ALL === $auth) {
//                $allow_ids = [];
//            } else {
//                $allow_ids = [0];
//                foreach ($auth['document'] as $document) {
//                    if ($document['can_read']) {
//                        $allow_ids[] = $document['function_id'];
//                    }
//                }
//            }

            $id = (int)$request->input('document_id');
            if(!$id){
	            return $this->error('文档id必传');
            }
            $result = $this->logic->getChapters($id);

            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
//            $auth = $request->document_user_auth;
//
//            if (APP_AUTH_ALL !== $auth) {
//                if (!isset($auth['document'][0])) {
//                    return $this->error('没有创建文档的权限!');
//                }
//            }
//
//            $this->logic->checkRepeatRequest($request->document_user_id);
//            $this->logic->checkWindControl($request->document_user_id,'max_number_added_per_day');

            $this->validate($request, [
                'name' => 'string|required|max:30',
                'sort' => 'integer|min:0',
                'document_id' => 'required|integer|min:1',
	            'parent_id' => 'required|integer|min:0',
            ], [
                'name.required' => '章节名称必填',
                'name.max' => '章节名最大３０个字符',
                'sort.min' => '排序最小值为０',
                'document_id.required' => '文档id必填',
	            'document_id.min' => '文档id最小为0',
	            'parent_id.required' => '父id必填',
            ]);

            $data['name'] = $request->input('name');
            $data['sort'] = $request->input('sort', 0);
            $data['document_id'] = $request->input('document_id');
            $data['parent_id'] = $request->input('parent_id');
            $result = $this->logic->createChapter($data);
            if ($result) {
                return $this->success($result);
            }

            return $this->error($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
//            $auth = $request->document_user_auth;
//            $id = $request->input('id');
//            if (!$id) {
//                return $this->error('id必传');
//            }
//
//            if (APP_AUTH_ALL !== $auth) {
//                if (!isset($auth['document'][$id]) || 0 === $auth['document'][$id]['can_modify']) {
//                    return $this->error('没有修改该文档的权限!');
//                }
//            }
//
//            $this->logic->checkRepeatRequest($request->document_user_id);

            $this->validate($request, [
	            'name' => 'string|required|max:30',
	            'sort' => 'integer|min:0',
	            'id' => 'required|integer|min:1',
            ], [
	            'name.required' => '章节名称必填',
	            'name.max' => '章节名最大３０个字符',
	            'sort.min' => '排序最小值为０',
	            'id.required' => '文档id必填',
	            'id.min' => '文档id最小为0',
            ]);

            $data['name'] = $request->input('name');
            $data['sort'] = $request->input('sort');
	        $id = $request->input('id');
            $result = $this->logic->updateChapter($id, $data);
            if ($result) {
                return $this->success($result);
            }

            return $this->error($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function publishOrCancel(Request $request)
    {
		try{
			$this->validate($request, [
				'id' => 'required',
				'is_show' => 'required|integer|min:0|max:1',
			], [
				'id.required' => '文档id必传',
				'is_show.required' => '发布状态必填',
			]);
			$id = $request->input('id');
			$is_show = $request->input('is_show');

			$auth = $request->document_user_auth;
			if (APP_AUTH_ALL !== $auth) {
				if (!isset($auth['document'][$id]) || 0 === $auth['document'][$id]['can_modify']) {
					return $this->error('没有修改该文档的权限!');
				}
			}

			$this->logic->publishOrCancel($id,$is_show);
			return $this->success(compact('id','is_show'));
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
    }

    public function show(Request $request)
    {
        try {
            $auth = $request->document_user_auth;
            $id = $request->input('id');
            if (!$id) {
                return $this->error('id必传');
            }
            if (APP_AUTH_ALL !== $auth) {
                if (!isset($auth['document'][$id]) || 0 === $auth['document'][$id]['can_read']) {
                    return $this->error('该文档不存在!');
                }
            }
            $result = $this->logic->getDocument($id);
            if ($result) {
                return $this->success($result);
            }

            return $this->error($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->input('id');
//            $auth = $request->document_user_auth;
//            if (!$id) {
//                return $this->error('id必传');
//            }
//            if (APP_AUTH_ALL !== $auth) {
//                if (!isset($auth['document'][$id]) || 0 === $auth['document'][$id]['can_delete']) {
//                    return $this->error('没有删除该文档的权限!!');
//                }
//            }
	        idb()->beginTransaction();
            $this->logic->deleteChapter($id);
			idb()->commit();
            return $this->success();
        } catch (\Exception $e) {
        	idb()->rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function saveContent(Request $request)
    {
    	try{
		    $this->validate($request, [
			    'chapter_id' => 'required|integer|min:1',
		    ], [
			    'chapter_id.required' => '文档id必填',
			    'chapter_id.min' => '文档id最小为0',
		    ]);
		    $id = $request->input('chapter_id');
		    $content = $request->input('content');
		    $this->logic->saveContent($id,$content);
		    return $this->success(['chapter_id'=>$id,'content'=>$content]);
	    }catch (\Exception $e){
		    return $this->error($e->getMessage());
	    }
    }
}
