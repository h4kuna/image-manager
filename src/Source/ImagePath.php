<?php

namespace h4kuna\ImageManager\Source;

class ImagePath
{

	public $url;
	public $fs;

	public function __construct($url, $fs)
	{
		$this->url = $url;
		$this->fs = $fs;
	}

}
