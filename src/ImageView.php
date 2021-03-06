<?php

namespace h4kuna\ImageManager;

use h4kuna\ImageManager,
	Nette\Utils;

class ImageView
{

	/** @var array */
	private $allowedResolution;

	/** @var Source\LocalSource */
	private $local;

	/** @var Source\PlaceholdSource */
	private $placehold;

	/** @var Source\RemoteSource */
	private $remote;

	/** @var DownloadInterface */
	private $download;

	public function __construct($allowedResolution, Source\LocalSource $local, Source\PlaceholdSource $placehold, DownloadInterface $download)
	{
		$this->allowedResolution = array_flip($allowedResolution);
		$this->local = $local;
		$this->placehold = $placehold;
		$this->download = $download;
	}

	public function setRemote(Source\RemoteSource $remote)
	{
		$this->remote = $remote;
	}

	/**
	 * @param string $name
	 * @param string $resolution
	 * @param int|string $method
	 * @throws ImageManager\ResolutionIsNotAllowedException
	 * @throws ImageManager\RemoteFileDoesNotExistsException
	 * @return string
	 */
	public function createUrl($name, $resolution, $method)
	{
		$this->checkResolution($resolution);
		return $this->createImagePath($name, $resolution, $method)->url;
	}

	/**
	 * @param string $name
	 * @param string $resolution
	 * @param int $method
	 * @throws ImageManager\ResolutionIsNotAllowedException
	 * @throws ImageManager\RemoteFileDoesNotExistsException
	 * @return bool
	 */
	public function send($name, $resolution, $method)
	{
		$this->checkResolution($resolution);
		$imagePath = $this->createImagePath($name, $resolution, $method);
		if ($imagePath->fs) {
			Utils\Image::fromFile($imagePath->fs)->send();
			return TRUE;
		} elseif ($imagePath->url) {
			Utils\Image::fromString($this->download->loadFromUrl($imagePath->url))->send();
			return TRUE;
		}
		return FALSE;
	}

	private function createImagePath($name, $resolution, $method)
	{
		if ($image = $this->local->createImagePath($name, $resolution, $method)) {
			return $image;
		} elseif ($this->remote && $image = $this->remote->createImagePath($resolution, $name, $method)) {
			return $image;
		}
		return $this->placehold->createImagePath($resolution);
	}

	private function checkResolution($resolution)
	{
		if ($resolution && !$this->allowedResolution || isset($this->allowedResolution[$resolution])) {
			return;
		}
		throw new ImageManager\ResolutionIsNotAllowedException($resolution);
	}

}
