<?php
/**
 * @author donknap
 * @date 19-4-23 下午6:38
 */

namespace W7\App\Model\Entity;


class Test extends BaseModel {
    public $timestamps = true;
    //protected $table = 'home';
    protected $primaryKey = 'id';
    //protected $fillable = [];
    protected $guarded = [];
    public $dateFormat = 'U';

}