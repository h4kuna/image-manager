<?php

namespace h4kuna\ImageManager;

use Nette\Http;

class Path
{

	/** @var string */
	private $imageTempDir;

	/** @var string */
	private $wwwImageTemp;

	/** @var string */
	private $sourceDir;

	/** @var string */
	private $hostUrl;

	/** @var string */
	private $basePath;

	public function __construct($imageTempDir, $wwwImageTemp, $sourceDir, Http\Request $request)
	{
		if ($wwwImageTemp && substr($wwwImageTemp, -1) == '/') {
			$wwwImageTemp = rtrim($wwwImageTemp, '/');
		}

		$this->imageTempDir = $imageTempDir;
		$this->wwwImageTemp = $wwwImageTemp;
		$this->sourceDir = $sourceDir;
		$url = $request->getUrl();
		$this->basePath = $url->getBasePath();
		$this->hostUrl = $url->getHostUrl();
	}

	public function getFilesystem($name, $resolution, $method)
	{
		return $this->getPath($name, $resolution, $method, DIRECTORY_SEPARATOR, $this->imageTempDir);
	}

	public function getUrl($name, $resolution, $method)
	{
		return $this->basePath . $this->getRawUrl($name, $resolution, $method);
	}

	public function getRawUrl($name, $resolution, $method)
	{
		return $this->getPath($name, $resolution, $method, '/', $this->wwwImageTemp);
	}

	private function getPath($name, $resolution, $method, $separator, $path)
	{
		return $path . $separator . $resolution . '-' . $method . $separator . $name;
	}

	public function getAbsoluteUrl($name, $resolution, $method)
	{
		return $this->hostUrl . $this->getUrl($name, $resolution, $method);
	}

	public function getSourceDir($filename)
	{
		return $this->sourceDir . DIRECTORY_SEPARATOR . $filename;
	}

}
