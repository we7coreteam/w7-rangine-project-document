<?php


namespace W7\App\Controller\Client;
use W7\App\Model\Logic\ChapterLogic;
use W7\Http\Message\Server\Request;


class ChapterController extends Controller{
	public function __construct()
	{
		$this->logic = new ChapterLogic();
	}

	public function chapters(Request $request)
	{
		try {
			$id = (int)$request->input('document_id');
			if(!$id){
				return $this->error('文档id必填');
			}
			$result = $this->logic->getChapters($id);
			return $this->success($result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function detail(Request $request) {
		try{
			$this->validate($request, [
				'id' => 'required|integer|min:1',
				'document_id' => 'required|integer|min:1',
			],[
				'id.required' => 'id必填',
				'id.integer' => 'id非法',
				'document_id.required' => '文档id必填',
				'document_id.integer' => '文档id非法'
			]);

			$id = $request->input('id');
			$document_id = $request->input('document_id');
			$res = $this->logic->getChapter($document_id,$id);
			if($res){
				return $this->success($res);
			}
			return $this->error('章节不存在');
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}

	public function search(Request $request)
	{
		try{
			$this->validate($request, [
				'keywords' => 'required',
				'document_id' => 'required|integer|min:1',
			],[
				'keywords.required' => '关键字必填',
				'document_id.required' => '文档id必填',
				'document_id.integer' => '文档id非法'
			]);

			$keyword = $request->input('keywords');
			$id = $request->input('document_id');
			$res = $this->logic->searchDocument($id,$keyword);
			return $this->success($res);
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}
}
