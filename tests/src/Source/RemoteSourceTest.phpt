<?php

namespace h4kuna\ImageManager\Source;

use h4kuna\ImageManager,
	Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class RemoteSourceTest extends \Tester\TestCase
{

	/** @var RemoteSource */
	private $remote;

	/** @var string */
	private $tempDir;

	public function __construct(RemoteSource $remote, $tempDir)
	{
		$this->remote = $remote;
		$this->tempDir = $tempDir;
	}

	public function testDownload()
	{
		try {
			$imagePath = $this->remote->createImagePath('200x100', 'foo.jpg', 0);
		} catch (ImageManager\RemoteFileDoesNotExistsException $e) {
			Assert::same('http://production-server.com/assets/temp/image/200x100-0/foo.jpg', $e->getMessage());
		}
		Assert::false(isset($imagePath));
	}

	/**
	 * @throws h4kuna\ImageManager\RemoteFileDoesNotExistsException
	 */
	public function testFail()
	{
		$this->remote->createImagePath('--x--', 'example.jpg', 0);
	}

}

/* @var $remote PlaceholdSource */
$remote = $container->getService('imageManager.remote');
$tempDir = $container->getParameters()['tempDir'];
(new RemoteSourceTest($remote, $tempDir))->run();
