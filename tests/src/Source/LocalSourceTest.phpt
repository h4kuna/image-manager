<?php

namespace h4kuna\ImageManager\Source;

use h4kuna\ImageManager,
	Nette\Utils,
	Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class LocalSourceTest extends \Tester\TestCase
{

	/** @var LocalSource */
	private $local;

	public function __construct(LocalSource $local)
	{
		$this->local = $local;
	}

	public function testSaveFile()
	{
		$imageSource = Utils\Image::fromFile(__DIR__ . '/../../config/noImage.jpg');
		$imagePath = $this->local->saveFile($imageSource, 'local.jpg', '80x60', Utils\Image::EXACT);

		$image = new ImageManager\Image($imagePath->fs, NULL);
		Assert::same(80, $image->getWidth());
		Assert::same(60, $image->getHeight());

		$imagePath2 = $this->local->createImagePath('local.jpg', '80x60', Utils\Image::EXACT);
		Assert::same($imagePath2->url, $imagePath->url);
		Assert::same($imagePath2->fs, $imagePath->fs);
	}

}

/* @var $local PlaceholdSource */
$local = $container->getService('imageManager.local');

(new LocalSourceTest($local))->run();
