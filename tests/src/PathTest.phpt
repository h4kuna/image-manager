<?php

namespace h4kuna\ImageManager;

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class PathTest extends \Tester\TestCase
{

	/** @var Path */
	private $path;

	/** @var string */
	private $tempDir;

	public function __construct(Path $path, $tempDir)
	{
		$this->path = $path;
		$this->tempDir = $tempDir;
	}

	public function testSourceDir()
	{
		Assert::same($this->tempDir . '/original/foo/image.jpg', $this->path->getSourceDir('foo/image.jpg'));
	}

	public function testUrl()
	{
		Assert::same('http://www.example.com/temp/image/100x100-5/foo/image.jpg', $this->path->getAbsoluteUrl('foo/image.jpg', '100x100', 5));
		Assert::same('/temp/image/100x100-5/foo/image.jpg', $this->path->getUrl('foo/image.jpg', '100x100', 5));
		Assert::same('temp/image/100x100-5/foo/image.jpg', $this->path->getRawUrl('foo/image.jpg', '100x100', 5));
	}

	public function testFilesystem()
	{
		Assert::same($this->tempDir . '/tempImage/100x100-5/foo/image.jpg', $this->path->getFilesystem('foo/image.jpg', '100x100', 5));
	}

}

/* @var $path ImageView */
$path = $container->getService('imageManager.path');
$tempDir = $container->getParameters()['tempDir'];
(new PathTest($path, $tempDir))->run();
