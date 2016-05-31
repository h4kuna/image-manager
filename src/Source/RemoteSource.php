<?php

namespace h4kuna\ImageManager\Source;

use h4kuna\ImageManager;

class RemoteSource
{

	/** @var string */
	private $hostUrl;

	/** @var LocalSource */
	private $local;

	/** @var ImageManager\Path */
	private $path;

	public function __construct($hostUrl, LocalSource $local, ImageManager\Path $path)
	{
		$this->hostUrl = $hostUrl;
		$this->local = $local;
		$this->path = $path;
	}

	/**
	 * @param string $resolution
	 * @param string $name
	 * @param int $method
	 * @return ImagePath
	 */
	public function createImagePath($resolution, $name, $method)
	{
		$url = $this->hostUrl . '/' . $this->path->getRawUrl($name, $resolution, $method);
		return $this->local->saveFile($url, $name, $resolution, $method);
	}

}
