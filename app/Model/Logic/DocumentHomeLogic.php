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
		return $model->typeName;
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
		return DocumentHome::query()->create($params);
	}


	/**
	 * 编辑首页文档设置
	 * @param $id
	 * @param $params
	 * @return int
	 */
	public function editHomeData($params){
		return DocumentHome::query()->where('id','=',intval($params['id']))->update($params);
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
			$result['data'][] = [
				'id' => $row->id,
				'user_id' => $row->user_id,
				'user' => $row->user->username,
				'document_id' => $row->document_id,
				'document_name' => $row->document->name,
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



}
