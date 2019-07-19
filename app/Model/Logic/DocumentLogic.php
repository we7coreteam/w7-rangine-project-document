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

    public function getDocuments($page)
    {
        return Document::select('id','name','icon','sort','is_show','category_id')->paginate(10,null,null,$page);
    }
}
