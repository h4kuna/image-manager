<?php

namespace h4kuna\ImageManager\Template;

use h4kuna,
	Latte,
	Nette\Utils;

class LatteMacro extends Latte\Macros\MacroSet
{

	private static $methods = [
		'default' => Utils\Image::FIT,
		'shrink' => Utils\Image::SHRINK_ONLY,
		'stretch' => Utils\Image::STRETCH,
		'fill' => Utils\Image::FILL,
		'exact' => Utils\Image::EXACT,
	];

	/**
	 * @param Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(Latte\Compiler $compiler, array $methods)
	{
		if ($methods) {
			self::$methods = $methods + self::$methods;
		}

		$me = new static($compiler);
		/**
		 * {img $name, $size[, $flags]]}
		 */
		$me->addMacro('img', [$me, 'macroImg'], NULL, [$me, 'macroAttrImg']);
		return $me;
	}

	/**
	 * @param Latte\MacroNode $node
	 * @param Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroImg(Latte\MacroNode $node, Latte\PhpWriter $writer)
	{
		return $writer->write('echo $template->getH4kunaImageView()->createUrl(' . self::getArgs($node) . ');');
	}

	/**
	 * @param Latte\MacroNode $node
	 * @param Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroAttrImg(Latte\MacroNode $node, Latte\PhpWriter $writer)
	{
		return $writer->write('?> src="<?php echo $template->getH4kunaImageView()->createUrl(' . self::getArgs($node) . ');?>" <?php');
	}

	private static function getArgs(Latte\MacroNode $node)
	{
		$args = h4kuna\Template\LattePhpTokenizer::toArray($node);
		if (!isset($args[2])) {
			$args[2] = 'default';
		}
		$args[2] = self::methodStringToInt(trim($args[2], '\'"'));

		$out = '';
		$chars = ['"', "'", '$'];
		foreach ($args as $value) {
			if ($out) {
				$out .= ', ';
			}
			if (!is_numeric($value)) {
				$char = substr($value, 0, 1);
				if (!in_array($char, $chars)) {
					$value = '"' . $value . '"';
				}
			}
			$out .= $value;
		}

		return $out;
	}

	/**
	 * @internal
	 * @param string|int $method
	 * @return int
	 */
	public static function methodStringToInt($method)
	{
		if (is_numeric($method)) {
			return (int) $method;
		}
		$int = 0;
		foreach (explode(';', $method) as $m) {
			if (isset(self::$methods[$m])) {
				$int |= self::$methods[$m];
			}
		}
		return $int;
	}

}
