<?php

namespace h4kuna\ImageManager;

interface DownloadInterface
{

	/**
	 * @param string $fromUrl
	 * @param string $to
	 * @return FALSE
	 */
	function save($fromUrl, $to);

	/**
	 * @param string $url
	 * @return string|FALSE
	 */
	function loadFromUrl($url);

}
