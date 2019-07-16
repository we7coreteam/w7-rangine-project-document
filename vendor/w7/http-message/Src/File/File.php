<?php
/**
 * @author donknap
 * @date 18-12-15 下午6:58
 */

namespace W7\Http\Message\File;


class File {
	protected $filename;
	protected $offset;
	protected $length;

	public function __construct(string $filename, int $offset = 0, int $length = 0) {
		if (!file_exists($filename)) {
			throw new \InvalidArgumentException('File not exists');
		}

		if (!empty($offset)) {
			$filesize = filesize($filename);
			if ($offset > $filesize) {
				throw new \InvalidArgumentException('Out of file range');
			}
		}

		$this->filename = $filename;
		$this->offset = $offset;
		$this->length = $length;
	}

	/**
	 * @return string
	 */
	public function getFilename(): string
	{
		return $this->filename;
	}

	/**
	 * @return int
	 */
	public function getOffset(): int
	{
		return $this->offset;
	}

	/**
	 * @return int
	 */
	public function getLength(): int
	{
		return $this->length;
	}
}