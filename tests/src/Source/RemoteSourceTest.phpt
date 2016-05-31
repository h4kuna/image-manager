<?php

namespace h4kuna\ImageManager\Source;

use Tester\Assert;

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
		$imagePath = $this->remote->createImagePath('200x100', 'foo.jpg', 0);
		Assert::same('/temp/image/200x100-0/foo.jpg', $imagePath->url);
		Assert::same($this->tempDir . '/tempImage/200x100-0/foo.jpg', $imagePath->fs);
		unlink($imagePath->fs);
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
