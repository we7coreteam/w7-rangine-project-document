<?php
namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Description;
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\UserAuthorization;

class DocumentLogic extends BaseLogic
{
    public function createDocument($data,$content)
    {
        $document = Document::create($data);
        $description = Description::create(['id'=>Document::getDescriptionId($document['id']),'content'=>$content]);
        if($document && $description){
            $document['content'] = $content;
        }
        UserAuthorization::create([
            'user_id'=>$data['creator_id'],
            'function_id'=>$document['id'],
            'function_name' => 'document',
            'can_read'=>1,
            'can_modify'=>1,
            'can_delete'=>1
        ]);
        $this->delete('auth_'.$data['creator_id']);
        return $document;
    }

    public function updateDocument($id,$data,$content)
    {
        $document = Document::where('id',$id)->update($data);
        $description = Description::where('id',Document::getDescriptionId($id))->update(['content'=>$content]);
        if($document && $description){
            $document['content'] = $content;
        }
        return $document;
    }

    public function getDocuments($page,$size,$category,$allow_ids)
    {
        return Document::select('id','name','icon','sort','is_show','category_id')
            //->where('is_show',Document::SHOW)
            ->when($category,function($query) use($category){
                return $query->where('category_id',$category);
            })
            ->when($allow_ids,function($query) use($allow_ids){
                return $query->whereIn('id',$allow_ids);
            })
            ->orderBy('sort','desc')
            ->paginate($size,null,null,$page);
    }

    public function getDocument($id)
    {
        $document = Document::select('id','name','icon','sort','is_show','category_id','updated_at')->where('id',$id)->first();
        if(!$document){
            throw new \Exception('该文档不存在！');
        }
        $description = Description::where('id',Document::getDescriptionId($id))->first();
        if($description){
        	$document['content'] = $description['content'];
		}else{
        	$document['content'] = '';
		}
        return $document;
    }

    public function deleteDocument($id)
	{
		$document = Document::select('id','name','icon','sort','is_show','category_id','updated_at')->where('id',$id)->first();
		if(!$document){
			throw new \Exception('该文档不存在！');
		}
		$document->delete();
		Description::where('id',Document::getDescriptionId($id))->delete();
	}
}
