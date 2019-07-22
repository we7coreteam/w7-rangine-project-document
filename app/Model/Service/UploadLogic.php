<?php
namespace W7\App\Model\Service;

class UploadLogic extends BaseLogic {
	protected $root_path = RUNTIME_PATH.'/upload/';
	protected $path = '';
	protected $name;

	public function root_path($path)
	{
		$this->root_path = $path;
		return $this;
	}

	public function path($path)
	{
		$this->path = $path;
		return $this;
	}

	public function name($name)
	{
		$this->name = $name;
		return $this;
	}

	public function upload($base64, $isPost=true)
	{
		if ($isPost) {
			$base64 = str_replace(' ', '+', $base64);
		}

		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
			//图片储存目录
			$dir = $this->root_path.$this->path;
			//文件名称
			!$this->name && $this->name = date("Ymdhis") . '_' . rand(10000, 99999);

			if (!is_dir($dir)) {
				//如果不存在就创建该目录
				mkdir($dir, 0777, true);
			}

			//获取图片后缀
			if ($result[2] == 'jpeg') {
				$filename = $this->name.'.jpg';
			} else {
				$filename = $this->name.'.'.$result[2];
			}
			//图片名称
			$image_url = $dir.$filename;
			file_put_contents($image_url, base64_decode(str_replace($result[1], '', $base64)));
			return $image_url;
		}
		return false;
	}
}
