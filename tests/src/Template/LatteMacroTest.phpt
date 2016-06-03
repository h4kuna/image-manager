<?php

namespace h4kuna\ImageManager\Template;

use Latte,
	Nette\Bridges\ApplicationLatte,
	Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class LatteMacroTest extends \Tester\TestCase
{

	/** @var ApplicationLatte\ILatteFactory */
	private $latteFactory;

	public function __construct(ApplicationLatte\ILatteFactory $latteFactory)
	{
		$this->latteFactory = $latteFactory;
	}

	public function testBasic()
	{
		list($node, $phpWrite, $macro) = $this->createNodeWrite();
		$node->args = 'foo/image.jpg, 200x200';
		Assert::same('?> src="<?php echo $template->getH4kunaImageView()->createUrl("foo/image.jpg", "200x200", 0);?>" <?php', $macro->macroAttrImg($node, $phpWrite));

		$node->args = '"foo/image.jpg", "200x200"';
		Assert::same('?> src="<?php echo $template->getH4kunaImageView()->createUrl("foo/image.jpg", "200x200", 0);?>" <?php', $macro->macroAttrImg($node, $phpWrite));

		$node->args = "'foo/image.jpg', '200x200'";
		Assert::same('?> src="<?php echo $template->getH4kunaImageView()->createUrl(\'foo/image.jpg\', \'200x200\', 0);?>" <?php', $macro->macroAttrImg($node, $phpWrite));

		$node->args = '$filePath, 200x200';
		Assert::same('?> src="<?php echo $template->getH4kunaImageView()->createUrl($filePath, "200x200", 0);?>" <?php', $macro->macroAttrImg($node, $phpWrite));

		$node->args = '$filePath, 200x200, basic';
		Assert::same('?> src="<?php echo $template->getH4kunaImageView()->createUrl($filePath, "200x200", 10);?>" <?php', $macro->macroAttrImg($node, $phpWrite));

		$node->args = '$filePath, 200x200, "basic"';
		Assert::same('?> src="<?php echo $template->getH4kunaImageView()->createUrl($filePath, "200x200", 10);?>" <?php', $macro->macroAttrImg($node, $phpWrite));
	}

	public function testMethodStrToInt()
	{
		Assert::same(1, LatteMacro::methodStringToInt('shrink'));
		Assert::same(3, LatteMacro::methodStringToInt('shrink;stretch'));
		Assert::same(0, LatteMacro::methodStringToInt('foo'));
		Assert::same(3, LatteMacro::methodStringToInt('3'));
	}

	private function createNodeWrite()
	{
		$compiler = $this->latteFactory->create()->getCompiler();
		$macro = LatteMacro::install($compiler, []);
		$node = new Latte\MacroNode($macro, 'img');
		$phpWrite = Latte\PhpWriter::using($node, $compiler);
		return [$node, $phpWrite, $macro];
	}

}

$latteFactory = $container->getService('latte.latteFactory');
(new LatteMacroTest($latteFactory))->run();
