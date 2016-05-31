<?php

namespace h4kuna\ImageManager\Template;

use Latte;

class LatteMacro extends Latte\Macros\MacroSet
{

	/**
	 * @param Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(Latte\Compiler $compiler)
	{
		$me = new static($compiler);
		/**
		 * {src $name, $size[, $flags]]}
		 */
		$me->addMacro('src', [$me, 'macroSrc'], NULL, [$me, 'macroAttrSrc']);
	}

	/**
	 * @param Latte\MacroNode $node
	 * @param Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroSrc(Latte\MacroNode $node, Latte\PhpWriter $writer)
	{
		return $writer->write('echo $template->getH4kunaImageView()->createUrl(%node.args);');
	}

	/**
	 * @param Latte\MacroNode $node
	 * @param Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroAttrSrc(Latte\MacroNode $node, Latte\PhpWriter $writer)
	{
		return $writer->write('?> src="<?php echo $template->getH4kunaImageView()->createUrl(%node.args);?>" <?php');
	}

}
