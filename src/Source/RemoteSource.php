<?php

namespace h4kuna\ImageManager\Source;

class RemoteSource
{

	/** @var string */
	private $hostUrl;

	/** @var LocalSource */
	private $local;

	public function __construct($hostUrl, LocalSource $local)
	{
		$this->hostUrl = $hostUrl;
		$this->local = $local;
	}

	/**
	 * @param string $resolution
	 * @param string $name
	 * @param int $method
	 * @return ImagePath
	 */
	public function createImagePath($resolution, $name, $method)
	{
		list($width, $height) = explode('x', $resolution);
		$url = str_replace(['$width$', '$height$', '$name$', '$method$'], [$width, $height, $name, $method], $this->hostUrl);
		return $this->local->saveFile($url, $name, $resolution, $method);
	}

}
