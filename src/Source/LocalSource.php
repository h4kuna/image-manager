<?php

namespace h4kuna\ImageManager\Source;

use h4kuna\ImageManager,
	Nette\Utils;

class LocalSource
{

	/** @var ImageManager\Path */
	private $path;

	/** @var ImageManager\DownloadInterface */
	private $download;

	/** @var PlaceholdSource */
	private $placehold;

	/** @var bool */
	private $absoluteUrl = FALSE;

	public function __construct(ImageManager\Path $path, ImageManager\DownloadInterface $download, PlaceholdSource $placehold)
	{
		$this->path = $path;
		$this->download = $download;
		$this->placehold = $placehold;
	}

	public function enableAbsoluteUrl()
	{
		$this->absoluteUrl = TRUE;
	}

	/**
	 * @param string $name
	 * @param string $resolution
	 * @param int $method
	 * @return ImagePath
	 */
	public function createImagePath($name, $resolution, $method)
	{
		$thumbFile = $this->path->getFilesystem($name, $resolution, $method);
		if (is_file($thumbFile)) {
			return new ImagePath($this->getPathUrl($name, $resolution, $method), $thumbFile);
		}
		$sourceImage = $this->path->getSourceDir($name);
		if (!is_file($sourceImage)) {
			return $this->placehold->createImagePath($resolution);
		}

		return $this->saveFile(Utils\Image::fromFile($sourceImage), $name, $resolution, $method);
	}

	/**
	 * @param Utils\Image|string $sourceFile
	 * @param string $name
	 * @param string $resolution
	 * @param int $method
	 * @return ImagePath
	 */
	public function saveFile($sourceFile, $name, $resolution, $method)
	{
		$filename = $this->path->getFilesystem($name, $resolution, $method);
		Utils\FileSystem::createDir(dirname($filename));

		if ($sourceFile instanceof Utils\Image) {
			$resolutionArray = explode('x', $resolution);
			$sourceFile->resize($resolutionArray[0], $resolutionArray[1], (int) $method)
				->save($filename);
		} elseif (!$this->download->save($sourceFile, $filename)) {
			throw new ImageManager\RemoteFileDoesNotExistsException($sourceFile);
		}
		return new ImagePath($this->getPathUrl($name, $resolution, $method), $filename);
	}

	private function getPathUrl($name, $resolution, $method)
	{
		if ($this->absoluteUrl) {
			return $this->path->getAbsoluteUrl($name, $resolution, $method);
		}
		return $this->path->getUrl($name, $resolution, $method);
	}

}
