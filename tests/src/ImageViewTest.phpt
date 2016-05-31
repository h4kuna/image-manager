<?php

namespace h4kuna\ImageManager;

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class ImageViewTest extends \Tester\TestCase
{

	/** @var ImageView */
	private $imageView;

	public function __construct(ImageView $imageView)
	{
		$this->imageView = $imageView;
	}

	/**
	 * @throws h4kuna\ImageManager\ResolutionIsNotAllowedException
	 */
	public function testAllowedResolution()
	{
		$this->imageView->createUrl('foo.jpg', '100x100', 0);
	}

	public function testMethodStrToInt()
	{
		Assert::same(1, ImageView::methodStringToInt('shrink'));
		Assert::same(3, ImageView::methodStringToInt('shrink,stretch'));
		Assert::same(0, ImageView::methodStringToInt('foo'));
		Assert::same(3, ImageView::methodStringToInt('3'));
	}

}

/* @var $imageView ImageView */
$imageView = $container->getService('imageManager.imageView');

(new ImageViewTest($imageView))->run();
