<?php
namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Description;
use W7\App\Model\Entity\Document;

class DocumentLogic extends BaseLogic
{
    public function createDocument($data,$content)
    {
        $document = Document::create($data);
        $description = Description::create(['id'=>Document::getDescriptionId($document['id']),'content'=>$content]);
        if($document && $description){
            $document['content'] = $content;
        }
        return $document;
    }

    public function getDocuments($page,$size,$category,$allow_ids)
    {
        return Document::select('id','name','icon','sort','is_show','category_id')
            ->when($category,function($query) use($category){
                return $query->where('category_id',$category);
            })
            ->when($allow_ids,function($query) use($allow_ids){
                return $query->whereIn('id',$allow_ids);
            })
            ->paginate($size,null,null,$page);
    }
}
