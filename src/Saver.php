<?php

namespace h4kuna\ImageManager;

use Nette\Http,
	Nette\Utils;

class Saver
{

	/** @var int[] */
	private $maxSize = [];

	/** @var Path */
	private $path;

	public function __construct(Path $path)
	{
		$this->path = $path;
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
		do {
			$relativePath = self::addSlashPath($path) . md5(microtime()) . '.' . strtolower($extension);
			$filename = $this->path->getSourceDir($relativePath);
		} while (is_file($filename));
		if ($this->maxSize) {
			$image->resize($this->maxSize['width'], $this->maxSize['height'], Utils\Image::SHRINK_ONLY);
		}
		Utils\FileSystem::createDir(dirname($filename));
		$image->save($filename);
		return new Image($this->path->getSourceDir(''), $relativePath);
	}

	private static function addSlashPath($path)
	{
		if (!$path) {
			return '';
		}
		return $path . DIRECTORY_SEPARATOR;
	}

}
