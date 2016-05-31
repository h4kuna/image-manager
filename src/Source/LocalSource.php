<?php

namespace h4kuna\ImageManager\Source;

use h4kuna\ImageManager,
	Nette\Http,
	Nette\Utils;

class LocalSource
{

	/** @var string */
	private $imageTempDir;

	/** @var string */
	private $wwwImageTemp;

	/** @var string */
	private $sourceDir;

	/** @var Http\Request */
	private $request;

	/** @var ImageManager\DownloadInterface */
	private $download;

	/** @var PlaceholdSource */
	private $placehold;

	public function __construct($imageTempDir, $wwwImageTemp, $sourceDir, Http\Request $request, ImageManager\DownloadInterface $download, PlaceholdSource $placehold)
	{
		if ($wwwImageTemp && substr($wwwImageTemp, -1) == '/') {
			$wwwImageTemp = rtrim($wwwImageTemp, '/');
		}

		$this->imageTempDir = $imageTempDir;
		$this->wwwImageTemp = $wwwImageTemp;
		$this->sourceDir = $sourceDir;
		$this->request = $request;
		$this->download = $download;
		$this->placehold = $placehold;
	}

	/**
	 * @param string $name
	 * @param string $resolution
	 * @param int $method
	 * @return ImagePath
	 */
	public function createImagePath($name, $resolution, $method)
	{
		$thumbFile = $this->getPathFs($name, $resolution, $method);
		if (is_file($thumbFile)) {
			return new ImagePath($this->getPathUrl($name, $resolution, $method), $thumbFile);
		}
		$sourceImage = $this->sourceDir . DIRECTORY_SEPARATOR . $name;
		if (!is_file($sourceImage)) {
			return $this->placehold->createImagePath($resolution);
		}

		return $this->saveFile(Utils\Image::fromFile($sourceImage), $name, $resolution, $method);
	}

	/**
	 *
	 * @param Utils\Image|string $sourceFile
	 * @param string $name
	 * @param string $resolution
	 * @param int $method
	 * @return ImagePath
	 */
	public function saveFile($sourceFile, $name, $resolution, $method)
	{
		$filename = $this->getPathFs($name, $resolution, $method);
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

	private function getPathFs($name, $resolution, $method)
	{
		return $this->getPath($name, $resolution, $method, DIRECTORY_SEPARATOR, $this->imageTempDir);
	}

	private function getPathUrl($name, $resolution, $method)
	{
		return $this->getPath($name, $resolution, $method, '/', rtrim($this->request->getUrl()->getBasePath() . $this->wwwImageTemp, '/'));
	}

	private function getPath($name, $resolution, $method, $separator, $path)
	{
		return $path . $separator . $resolution . '-' . $method . $separator . $name;
	}

}
