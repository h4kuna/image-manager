<?php

namespace h4kuna\Test\Http;

use Nette\Http;

class RequestFactory
{

	/** @var Http\UrlScript */
	private $urlScript;

	/** @return Http\Request */
	public function create($url)
	{
		$files = $cookies = $method = $query = $headers = $remoteAddress = $remoteHost = $post = NULL;
		$this->urlScript = new Http\UrlScript($url);
		return new Http\Request($this->urlScript, $query, $post, $files, $cookies, $headers, $method, $remoteAddress, $remoteHost);
	}

	/** @return Http\UrlScript */
	public function getUrlScript()
	{
		return $this->urlScript;
	}

}
