<?php

namespace h4kuna\ImageManager\Download;

use h4kuna\ImageManager;

class FileContent implements ImageManager\DownloadInterface
{

	public function save($from, $to)
	{
		$data = $this->loadFromUrl($from);
		if (!$data) {
			return FALSE;
		}
		return @file_put_contents($to, $data);
	}

	public function loadFromUrl($url)
	{
		return @file_get_contents($url);
	}

}
