<?php

namespace h4kuna\Macros;

use Nette;
use Nette\Latte\Compiler;
use Nette\Latte\MacroNode;
use Nette\Latte\PhpWriter;
use Nette\Latte\CompileException;

/**
 * @author Jan Brabec <brabijan@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 */
class Latte extends Nette\Latte\Macros\MacroSet {

    private $used = FALSE;

    /**
     * @param \Nette\Latte\Compiler $compiler
     *
     * @return ImgMacro|\Nette\Latte\Macros\MacroSet
     */
    public static function install(Compiler $compiler) {
        $me = new static($compiler);
        /**
         * {img [namespace/]$name[, $size[, $flags]]}
         */
        $me->addMacro('img', array($me, 'macroImg'), NULL, array($me, 'macroAttrImg'));
        return $me;
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroImg(MacroNode $node, PhpWriter $writer) {
        $namespace = NULL;
        $arguments = $this->createArguments($node, $writer, $namespace);
        return $writer->write('echo %escape($_imageManager->setNamespace(' . $writer->formatWord(trim($namespace)) . ')->request(' . implode(", ", $arguments) . '))');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroAttrImg(MacroNode $node, PhpWriter $writer) {

        $namespace = NULL;
        $arguments = $this->createArguments($node, $writer, $namespace);
        return $writer->write('?> src="<?php echo %escape($_imageManager->setNamespace(' . $writer->formatWord(trim($namespace)) . ')->request(' . implode(", ", $arguments) . '))?>" <?php');
    }

    private function createArguments(MacroNode $node, PhpWriter $writer, &$namespace) {
        $arguments = Helpers::prepareMacroArguments($node->args);
        if ($arguments["name"] === NULL) {
            throw new CompileException("Please provide filename.");
        }

        $namespace = $arguments["namespace"];
        unset($arguments["namespace"]);
        $arguments = array_map(function ($value) use ($writer) {
            return $writer->formatWord($value);
        }, $arguments);
        return $arguments;
    }

    public function finalize() {
        if ($this->used) {
            return array();
        }
        $this->used = TRUE;
        return array(
            'if(isset($control)) { $template->_imageManager = $control->getPresenter()->getContext()->getByType("h4kuna\ImageManager"); }',
            NULL
        );
    }

}
