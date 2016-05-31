<?php

namespace h4kuna\ImageManager;

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class ImageTest extends \Tester\TestCase
{

	public function testResolution()
	{
		$image = $this->createImage();
		Assert::same(489, $image->getWidth());
		Assert::same(604, $image->getHeight());
	}

	public function testSize()
	{
		$image = $this->createImage();
		Assert::same(55154, $image->getFileInfo()->getSize());
	}

	public function testHtml()
	{
		$image = $this->createImage();
		Assert::same('<img src="foo.jpg" width="489" height="604">', (string) $image->toHtml('foo.jpg'));

		Assert::same('<img src="foo.jpg" alt="alt-text" width="489" height="604">', (string) $image->toHtml('foo.jpg', 'alt-text'));
	}

	public function testPath()
	{
		$image = $this->createImage();
		Assert::same('config/noImage.jpg', $image->getRelativePath());
		Assert::same(__DIR__ . '/../config/noImage.jpg', $image->getPathname());
	}

	private function createImage()
	{
		return new Image(__DIR__ . '/../', 'config/noImage.jpg');
	}

}

(new ImageTest)->run();
