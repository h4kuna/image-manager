<?php

namespace h4kuna\ImageManager\Source;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class PlaceholdSourceTest extends \Tester\TestCase
{

	/** @var PlaceholdSource */
	private $placehold;

	public function __construct(PlaceholdSource $placehold)
	{
		$this->placehold = $placehold;
	}

	public function testUrl()
	{
		$imagePath = $this->placehold->createImagePath('99x99');
		Assert::same('https://placeholdit.imgix.net/~text?size=14&txt=99x99&h=99&w=99', $imagePath->url);
		Assert::null($imagePath->fs);
	}

	public function testFail()
	{
		$imagePath = $this->placehold->createImagePath(NULL);
		Assert::same(NULL, $imagePath);
	}

}

/* @var $placehold PlaceholdSource */
$placehold = $container->getService('imageManager.placehold');

(new PlaceholdSourceTest($placehold))->run();
