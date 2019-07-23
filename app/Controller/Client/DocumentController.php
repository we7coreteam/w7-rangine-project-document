<?php


namespace W7\App\Controller\Client;
use W7\App\Model\Logic\DocumentLogic;
use W7\Http\Message\Server\Request;


class DocumentController extends Controller{
	public function __construct()
	{
		$this->logic = new DocumentLogic();
	}

	public function detail(Request $request) {
		try{
			$this->validate($request, [
				'id' => 'required|integer|min:1',
			],[
				'id.required' => 'id必填',
				'id.integer' => 'id非法'
			]);

			$id = $request->input('id');
			$res = $this->logic->getDocument($id);
			if($res && $res['is_show'] == 1){
				return $this->success($res);
			}
			return $this->error('文档不存在');
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}

	public function search(Request $request)
	{
		try{
			$this->validate($request, [
				'keyword' => 'required',
			],[
				'keyword.required' => '关键字必填',
			]);

			$keyword = $request->input('keyword');
			$res = $this->logic->searchDocument($keyword);
			return $this->success($res);
		}catch (\Exception $e){
			return $this->error($e->getMessage());
		}
	}
}
