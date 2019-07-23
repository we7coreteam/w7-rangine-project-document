<?php
namespace W7\App\Model\Logic;

use W7\App\Model\Entity\DocumentContent;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserAuthorization;

class DocumentLogic extends BaseLogic
{
    public function createDocument($data, $content)
    {
        $document = Document::create($data);
        $description = DocumentContent::create(['document_id' => $document['id'], 'content' => $content]);
        if ($document && $description) {
            $document['content'] = $content;
        }
        UserAuthorization::create([
            'user_id' => $data['creator_id'],
            'function_id' => $document['id'],
            'function_name' => 'document',
            'can_read' => 1,
            'can_modify' => 1,
            'can_delete' => 1,
        ]);
        $this->delete('auth_'.$data['creator_id']);
	    $this->increment('max_number_added_per_day_'.date('Ymd').'_'.$data['creator_id']);
        return $document;
    }

    public function updateDocument($id, $data, $content)
    {
        $document = Document::where('id', $id)->update($data);
        $description = DocumentContent::where('document_id', $id)->update(['content' => $content]);
        $this->delete($id);
        return true;
    }

    public function publishOrCancel($id,$is_show)
    {
		$document = Document::find($id);
		if($document){
			$document->is_show = $is_show;
			$document->save();
			$this->delete($id);
			return true;
		}
		throw new \Exception('该文档不存在');
    }

    public function getDocuments($page, $size, $category, $allow_ids,$is_show,$keyword)
    {
        return Document::when($category, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })
            ->when($allow_ids, function ($query) use ($allow_ids) {
                return $query->whereIn('id', $allow_ids);
            })
	        ->where(function ($query) use ($keyword){
	        	if($keyword){
			        $user_ids = User::where('username','like',$keyword)->pluck('id')->toArray();
			        $query->whereIn('creator_id',$user_ids)->orWhere('name','like','%'.$keyword.'%');
		        }
	        })
	        ->when($is_show !== null,function ($query) use ($is_show){
		        return $query->where('is_show', $is_show);
	        })
            ->orderBy('sort', 'desc')
            ->paginate($size, null, null, $page);
    }

    public function getDocument($id)
    {
    	if($this->get($id)){
    		return $this->get($id);
	    }
        $document = Document::where('id', $id)->first();
        if (!$document) {
            throw new \Exception('该文档不存在！');
        }
        $description = DocumentContent::where('document_id', $id)->first();
        if ($description) {
            $document['content'] = $description['content'];
        } else {
            $document['content'] = '';
        }
        $this->set($id,$document);
        return $document;
    }

    public function searchDocument($keyword)
    {
    	$document_ids = DocumentContent::where('content','like','%'.$keyword.'%')->pluck('document_id')->toArray();
    	$documents = Document::whereIn('id',$document_ids)->where('is_show',1)->get()->toArray();
	    foreach ($documents as &$document) {
		    $document['content'] = DocumentContent::find($document['id'])->content??'';
    	}
    	return $documents;
    }

    public function deleteDocument($id)
    {
        $document = Document::select('id', 'name', 'icon', 'sort', 'is_show', 'category_id', 'updated_at')->where('id', $id)->first();
        if (!$document) {
            throw new \Exception('该文档不存在！');
        }
        $document->delete();
        DocumentContent::where('document_id', $id)->delete();
    }
}
