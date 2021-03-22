<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Model\Logic;

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\DocumentHome;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentHomeLogic extends BaseLogic
{
	use InstanceTraiter;

	/**
	 * 获取分类
	 * @return string[]
	 */
	public function getTypeData(){
		$model = new DocumentHome();
		$data = [];
        foreach ($model->typeName as $key=>$val){
        	$data[] = ['label'=>$val,'val'=>$key];
		}
        return array_values($data);
	}

	/**
	 * 添加 首页文档设置
	 * @param $params
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
	 */
	public function addHomeData($params){
		//公告只允许添加一个
		if ($params['type'] == 1){
			$checkCount = DocumentHome::query()->where('type','=',$params['type'])->count('id');
			if ($checkCount >= 1){
				throw new ErrorHttpException('公告只允许添加一条');
			}
		}
		//检测是否为空文档
		$checkDocument = $this->getByChapter(intval($params['document_id']));
		if (!$checkDocument){
			throw new ErrorHttpException('请勿选择空文档');
		}
		return DocumentHome::query()->create($params);
	}


	/**
	 * 编辑首页文档设置
	 * @param $id
	 * @param $params
	 * @return int
	 */
	public function editHomeData($params){
		//检测是否为空文档
		$checkDocument = $this->getByChapter(intval($params['document_id']));
		if (!$checkDocument){
			throw new ErrorHttpException('请勿选择空文档');
		}
		return DocumentHome::query()->where('id','=',intval($params['id']))->update($params);
	}



	/**
	 * 删除文档数据
	 * @param $id
	 */
	public function delHomeData($id){
		$check = DocumentHome::query()->where('id','=',$id)->first();
		if (!$check){
			throw new ErrorHttpException('数据不存在');
		}
		return DocumentHome::query()->where('id','=',$id)->delete();
	}



	/**
	 * 获取详情
	 * @param $id
	 */
	public function getByHomeData($id){
		$id = intval($id);
		if (empty($id)) {
			return [];
		}
		$result =  DocumentHome::query()->with('document')->with('user')->where('id','=',$id)->first();
        $data = [];
        if ($result){
			$data = [
				'id' => $result->id,
				'user_id' =>$result->user_id,
				'user' => $result->user->username,
				'type' => $result->type,
				'type_name' => $result->type_name,
				'document_id' => $result->document_id,
				'document_name' => $result->document->name,
				'url' => $result->url,
				'description' => $result->description,
				'sort' => $result->sort,
				'created_at' => $result->created_at->toDateTimeString()
			];
		}
		return $data;
	}


	/**
	 * 获取列表数据
	 * @param $type
	 * @param $page
	 * @param $pageSize
	 * @return array
	 */
	public function getListData($type,$page,$pageSize){
		$query = DocumentHome::query()->with('document')->with('user');
		if (isset($type) && $type > 0 ){
			$query = $query->where('type','=',$type);
		}
		$query = $query->orderBy('sort','asc');
		$list = $query->paginate($pageSize, ['id','user_id','document_id','type','sort','logo','url','description','created_at'], 'page', $page);
		foreach ($list->items() as $i => $row) {
			//获取文档第一个章节数据
			$chapter = $this->getByChapter(intval($row['document_id']));
			$result['data'][] = [
				'id' => $row->id,
				'user_id' => $row->user_id,
				'user' => $row->user->username,
				'document_id' => $row->document_id,
				'document_name' => $row->document->name,
				'first_chapter_id' => $chapter ? $chapter['chapter_id'] : 0,
				'first_chapter_name' => $chapter ? $chapter['chapter_name'] : '',
				'logo' => $row->logo,
				'url' => $row->url,
				'description' => $row->description,
				'sort' => $row->sort,
				'type' => $row->type,
				'type_name' => $row->type_name,
				'created_at' => $row->created_at->toDateTimeString()
			];
		}
		$result['page_count'] = $list->lastPage();
		$result['total'] = $list->total();
		$result['page_current'] = $list->currentPage();

		return $result;
	}

	/**
	 * 模糊查询 文档名称
	 * @param $name
	 * @param $isPublic
	 * @return array
	 */
	public function queryDocument($name,$isPublic){
		$query = Document::query();
		if (!empty($name)){
			$query->where('name', 'LIKE', "%{$name}%");
		}
		if (!empty($isPublic)){
			$query->where('is_public', '=', $isPublic);
		}
		$list = $query->orderByDesc('created_at')->limit(10)->get();
		$result = [];
		if (!empty($list->toArray())){
			foreach ($list->toArray() as $i => $row) {
				$result[] = [
					'document_id' => $row['id'],
					'name' => $row['name'],
				];
			}
		}
		return $result;
	}


	/**
	 * 获取前端 首页公告数据
	 */
	public function getDocumentNotice(){
        $notice = DocumentHome::query()->select('id','type','document_id')->where('type','=',1)->first();
        $data = [];
        if ($notice){
        	//项目信息
			$document = DocumentLogic::instance()->getById($notice->document_id);
        	$data['document_id'] = $notice->document_id;
        	$data['document_name'] = $document->name;
        	//章节信息
			$chapter = Document\Chapter::query()
				->select('id', 'name', 'sort', 'parent_id', 'is_dir', 'document_id','created_at')
				->where('document_id', $notice->document_id)
				->where('is_dir','<>',Document\Chapter::IS_DIR)
				->orderByDesc('created_at')->limit(8)->get()->toArray();
			foreach ($chapter as $key => $item){
				$data['chapter'][$key]['chapter_id'] = $item['id'];
				$data['chapter'][$key]['chapter_name'] = $item['name'];
				$data['chapter'][$key]['created_at'] = date('Y.n.d',$item['created_at']);
			}
		}
        return  $data;
	}


	/**
	 * 获取前端 类型一数据
	 */
	public function getDocumentTypeI(){
		//获取类型一数据
		$typeList = DocumentHome::query()
			        ->select('id','type','sort','document_id','logo','description','created_at')
			        ->where('type','=',2)
			        ->orderBy('sort','asc')
					->orderByDesc('created_at')
			        ->limit(4)->get()->toArray();

		$data = [];
		if ($typeList){
			foreach ($typeList as $key=>$item){
				$document = DocumentLogic::instance()->getById($item['document_id']);
				$data[$key]['document_id'] = $item['document_id'];
				$data[$key]['document_name'] = $document->name;
				//获取文档第一个章节数据
				$chapter = $this->getByChapter(intval($item['document_id']));
				$data[$key]['first_chapter_id'] = $chapter ? $chapter['chapter_id'] : 0;
				$data[$key]['first_chapter_name'] = $chapter ? $chapter['chapter_name'] : '';
				$data[$key]['logo'] = $item['logo'];
				$data[$key]['description'] = $item['description'];
				$data[$key]['created_at'] = date('Y-m-d H:i:s',$item['created_at']);
			}
		}
		return $data;
	}


	/**
	 * 获取前端 类型二数据
	 */
	public function getDocumentTypeII(){
		//获取类型二数据
		$typeList = DocumentHome::query()
			->select('id','type','sort','document_id','created_at')
			->where('type','=',3)
			->orderBy('sort','asc')
			->orderByDesc('created_at')
			->limit(4)->get()->toArray();
		$data = [];
		if ($typeList){
			foreach ($typeList as $key=>$item){
				$document = DocumentLogic::instance()->getById($item['document_id']);
				$data[$key]['document_id'] = $item['document_id'];
				$data[$key]['document_name'] = $document->name;
                //获取章节数据
				$data[$key]['chapter'] = $this->getByChapterList($item['document_id']);
			}
		}
		return $data;
	}



	/**
	 *前端 搜索文档
	 * @param $keyword
	 */
	public function searchDocument($keyword,$page,$pageSize,$isPublic = 1){
         $query = Document::query();
         if (!empty($keyword)){
			 $query->where('name', 'LIKE', "%{$keyword}%");
		 }
		 if (!empty($isPublic)){
			$query->where('is_public', '=', $isPublic);
		 }

		 $list = $query->select('id','name','cover','is_public')
			           ->orderByDesc('created_at')
			           ->paginate($pageSize, '*', 'page', $page)->toArray();
		 //数据处理
		 if (is_array($list['data']) && $list['data']){
		 	 foreach ($list['data'] as $key => &$item){
                $data =  $this->getByChapter($item['id']);
                $item['chapter_id'] = !empty($data)? $data['chapter_id'] : 0;
                $item['chapter_name'] = !empty($data)? $data['chapter_name'] : '';
                $item['chapter_content'] = !empty($data)? mb_strimwidth($data['chapter_content'], 0, 266, '...','utf-8') : '';
                $item['nav'] = $this->buildNavigationSun($item['chapter_id']);
			 }
		 }
		 return $list;
	}


	/**
	 * 面包屑导航
	 * @param $chapterId
	 * @param string $str
	 * @param int $i
	 * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|mixed|string
	 */
	public function buildNavigationSun($chapterId, $str = '', $i = 0)
	{
		$i++;
		if ($i > 50) {
			//循环大于100，不再处理
			return $str;
		}
		$chapter = Document\Chapter::query()->find($chapterId);
		if ($chapter) {
			if (!$str) {
				//如果是根级
				$str = $chapter->name;
			} else {
				//如果是上级
				$str = $chapter->name . '>' . $str;
			}
			if ($chapter->parent_id) {
				$str = $this->buildNavigationSun($chapter->parent_id, $str);
			}
		}
		return $str;
	}


	/**
	 * 获取文档章节 内容
	 * @param $documentId
	 * @return array
	 */
	private function getByChapter($documentId)
	{
		$chapter = Document\Chapter::query()
			->select('id', 'name', 'sort', 'parent_id', 'is_dir', 'default_show_chapter_id')
			->where('document_id', $documentId)
			->orderBy('parent_id', 'asc')
			->orderBy('sort', 'asc')->first();
		$data = [];
		if ($chapter){
			if ($chapter->is_dir == Document\Chapter::IS_DIR){
				return $this->getByChapterChildren($chapter->id);
			}else{
				$content = Document\ChapterContent::query()->where('chapter_id','=',$chapter->id)->first();
				$data['chapter_id'] = $chapter->id;
				$data['chapter_name'] = $chapter->name;
				$data['chapter_content'] = $content->content ? : '';
			}
		}
        return $data;
	}


	/**
	 * 递归查找 文档章节内容
	 * @param $chapterId
	 */
    private function getByChapterChildren($chapterId){
    	$chapter = Document\Chapter::query()
			->select('id', 'name', 'sort', 'parent_id', 'is_dir', 'default_show_chapter_id')
			->where('parent_id', $chapterId)
			->orderBy('sort', 'asc')->first();
		$data = [];
    	if ($chapter){
    		if ($chapter->is_dir == Document\Chapter::IS_DIR){
    			return $this->getByChapterChildren($chapter->id);
			}else{
				$content = Document\ChapterContent::query()->where('chapter_id','=',$chapter->id)->first();
				$data['chapter_id'] = $chapter->id;
				$data['chapter_name'] = $chapter->name;
				$data['chapter_content'] = $content->content ? $content->content : '';
			}
		}
        return $data;
	}


	/**
	 * 获取章节数据列表
	 * @param $documentId
	 * @param int $limit
	 * @return array
	 */
	public function getByChapterList($documentId,$limit=5){
		//获取章节信息
		$chapter = Document\Chapter::query()
			->select('id', 'name', 'sort', 'parent_id', 'is_dir', 'document_id','created_at')
			->where('document_id', $documentId)
			->where('is_dir','<>',Document\Chapter::IS_DIR)
			->orderByDesc('created_at')->limit($limit)->get()->toArray();
		$data = [];
		if ($chapter){
			foreach ($chapter as $key=>$item){
				$data[$key]['chapter_id'] = $item['id'];
				$data[$key]['chapter_name'] = $item['name'];
				$data[$key]['created_at'] = date('Y-m-d H:i:s',$item['created_at']);
			}
		}
		return $data;
	}




}
