<?php

namespace h4kuna\ImageManager\Source;

use Nette\Http;

class PlaceholdSource
{

	/** @var Http\Url */
	private $url;

	/** @var array */
	private $parameters = [
		'size' => 14,
		'txt' => NULL,
		'h' => NULL,
		'w' => NULL
	];

	public function __construct()
	{
		$this->url = new Http\Url('https://placeholdit.imgix.net/~text');
	}

	public function setText($text)
	{
		$this->parameters['txt'] = $text;
	}

	public function setSize($size)
	{
		$this->parameters['size'] = $size;
	}

	public function addParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * @param string $resolution
	 * @return ImagePath|NULL
	 */
	public function createImagePath($resolution)
	{
		$parameters = $this->parameters;
		@list($parameters['w'], $parameters['h']) = explode('x', $resolution);
		if (!$parameters['w'] || !$parameters['h']) {
			return NULL;
		}
		if (!$parameters['txt']) {
			$parameters['txt'] = $resolution;
		}
		$url = clone $this->url;
		$url->setQuery($parameters);
		return new ImagePath($url->getAbsoluteUrl(), NULL);
	}

}
