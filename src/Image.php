<?php

namespace h4kuna\ImageManager;

use Nette\Utils;

class Image
{

	/** @var array */
	private $imageInfo;

	/** @var string */
	private $relativePath;

	/** @var string  */
	private $pathname;

	/** @var \SplFileInfo */
	private $fileInfo;

	public function __construct($sourceDir, $relativePath)
	{
		$this->pathname = $sourceDir . $relativePath;
		$this->relativePath = $relativePath;
	}

	/** @return int */
	public function getHeight()
	{
		return $this->getImageInfo()[1];
	}

	/** @return int */
	public function getWidth()
	{
		return $this->getImageInfo()[0];
	}

	public function getRelativePath()
	{
		return $this->relativePath;
	}

	/**
	 * @param string $url
	 * @param string|NULL $alt
	 * @return Utils\Html
	 */
	public function toHtml($url, $alt = NULL)
	{
		$img = Utils\Html::el('img');
		$img->addAttributes(array(
			'src' => $url,
			'alt' => $alt,
			'width' => $this->getWidth(),
			'height' => $this->getHeight()
		));
		return $img;
	}

	public function getFileInfo()
	{
		if ($this->fileInfo) {
			return $this->fileInfo;
		}
		return $this->fileInfo = new \SplFileInfo($this->pathname);
	}

	public function getPathname()
	{
		return $this->pathname;
	}

	public function getImageInfo()
	{
		if ($this->imageInfo === NULL) {
			$this->imageInfo = @getimagesize($this->pathname); // @ - files smaller than 12 bytes causes read error
		}
		return $this->imageInfo;
	}

	public function __toString()
	{
		return $this->pathname;
	}

}
