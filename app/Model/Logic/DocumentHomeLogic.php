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

use W7\App\Model\Entity\DocumentHome;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DocumentHomeLogic extends BaseLogic
{
	use InstanceTraiter;

	//获取分类
	public function getTypeData(){
		$model = new DocumentHome();
		return $model->typeName;
	}




}
