<?php
/**
 * @author donknap
 * @date 19-4-23 下午6:38
 */

namespace W7\App\Model\Entity;


use W7\Core\Database\ModelAbstract;

class BaseModel extends ModelAbstract {
    public $timestamps = true;
    //protected $table = 'home';
    //protected $primaryKey = 'id';
    //protected $fillable = [];
    protected $guarded = [];
    public $dateFormat = 'U';

}