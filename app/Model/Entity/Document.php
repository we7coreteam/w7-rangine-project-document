<?php
/**
 * @author donknap
 * @date 19-4-23 下午6:38
 */

namespace W7\App\Model\Entity;


class Document extends BaseModel {
//    protected $casts = [
//        'is_show' => 'boolean',
//        'sort' => 'integer'
//    ];

    public static function getDescriptionId($id)
    {
        return 'document_'.$id;
    }
}
