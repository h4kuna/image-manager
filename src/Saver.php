<?php

namespace h4kuna\ImageManager;

use Nette\Http,
	Nette\Utils;

class Saver
{

	/** @var int[] */
	private $maxSize = [];

	/** @var string */
	private $saveDir;

	/**
	 * @param string $saveDir - slash on end of path is required
	 */
	public function __construct($saveDir)
	{
		$this->saveDir = self::addSlashPath($saveDir);
	}

	public function setMaxSize($maxSize)
	{
		$this->maxSize = array_combine(['width', 'height'], explode('x', $maxSize));
	}

	/**
	 * @param Http\FileUpload $fileUpload
	 * @param string|NULL $path
	 * @return Image
	 */
	public function saveFileUpload(Http\FileUpload $fileUpload, $path = NULL)
	{
		return $this->saveImage($fileUpload->toImage(), $path, pathinfo($fileUpload->getName(), PATHINFO_EXTENSION));
	}

	/**
	 * @param string $filename
	 * @param string|NULL $path
	 * @return Image
	 */
	public function save($filename, $path = NULL)
	{
		return $this->saveImage(Utils\Image::fromFile($filename), $path, pathinfo($filename, PATHINFO_EXTENSION));
	}

	/**
	 * @param Utils\Image $image
	 * @param string $path
	 * @param string $extension
	 * @return Image
	 */
	public function saveImage(Utils\Image $image, $path, $extension)
	{
		$relative = self::addSlashPath($path);
		Utils\FileSystem::createDir($this->saveDir . $relative);
		do {
			$relativePath = $relative . md5(microtime()) . '.' . strtolower($extension);
			$filename = $this->saveDir . $relativePath;
		} while (is_file($filename));
		if ($this->maxSize) {
			$image->resize($this->maxSize['width'], $this->maxSize['height'], Utils\Image::SHRINK_ONLY);
		}
		$image->save($filename);
		return new Image($this->saveDir, $relativePath);
	}

	private static function addSlashPath($path)
	{
		if (!$path) {
			return '';
		}
		return rtrim($path, '\/') . DIRECTORY_SEPARATOR;
	}

}
