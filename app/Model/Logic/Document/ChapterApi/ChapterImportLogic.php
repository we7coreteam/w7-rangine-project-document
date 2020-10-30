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

namespace W7\App\Model\Logic\Document\ChapterApi;

//返回演示数据demo
class ChapterImportLogic extends ChapterCommonLogic
{
	public function keyWordToData($keyWord)
	{
		$data = [];
		$str = '';
		$data1 = explode("\n", $keyWord);
		if (count($data1)) {
			foreach ($data1 as $key => $val) {
				$item = '';
				$itemData = explode(':', $val);
				if (substr($itemData[0], 0, 2) == '//') {
					continue;
				}
				if (count($itemData) > 1) {
					$start = strlen($itemData[0] . ':');
					$last = substr($val, $start);
					//首字符去空
					if (strlen($last) > 1) {
						if (substr($last, 0, 1) == ' ') {
							$last = substr($last, 1);
						}
					}
					$item = $itemData[0] . '=' . $last;
				} else {
					$item = $itemData[0] . '=';
				}
				if ($item) {
					if ($str) {
						$str .= '&' . $item;
					} else {
						$str = $item;
					}
				}

				if ($key > 4) {
					break;
				}
			}
		}
		if ($str) {
			$data = parse_str($str);
		}
		return $data;
	}
}
