<?php

namespace h4kuna\Macros;

use h4kuna\Images\ImagePipe;
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

    /**
     * @var bool
     */
    private $isUsed = FALSE;

    /**
     * @param \Nette\Latte\Compiler $compiler
     *
     * @return ImgMacro|\Nette\Latte\Macros\MacroSet
     */
    public static function install(Compiler $compiler) {
        $me = new static($compiler);
        // todo: předání parametrů s velikostí

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
        $this->isUsed = TRUE;
        $namespace = NULL;
        $arguments = $this->createArguments($node, $writer, $namespace);
        return $writer->write('echo %escape($_imagePipe->setNamespace(' . $writer->formatWord(trim($namespace)) . ')->request(' . implode(", ", $arguments) . '))');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroAttrImg(MacroNode $node, PhpWriter $writer) {
        $this->isUsed = TRUE;
        $namespace = NULL;
        $arguments = $this->createArguments($node, $writer, $namespace);
        return $writer->write('?> src="<?php echo %escape($_imagePipe->setNamespace(' . $writer->formatWord(trim($namespace)) . ')->request(' . implode(", ", $arguments) . '))?>" <?php');
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

    /**
     */
    public function initialize() {
        $this->isUsed = FALSE;
    }

    /**
     * Finishes template parsing.
     *
     * @return array(prolog, epilog)
     */
    public function finalize() {
        if (!$this->isUsed) {
            return array();
        }

        return array(
            get_called_class() . '::validateTemplateParams($template);',
            NULL
        );
    }

    /**
     * @param \Nette\Templating\Template $template
     * @throws \Nette\InvalidStateException
     */
    public static function validateTemplateParams(Nette\Templating\Template $template) {
        $params = $template->getParameters();
        if (!isset($params['_imagePipe']) || !$params['_imagePipe'] instanceof ImagePipe) {
            $where = isset($params['control']) ?
                    " of component " . get_class($params['control']) . '(' . $params['control']->getName() . ')' : NULL;

            throw new Nette\InvalidStateException(
            'Please provide an instanceof Img\\ImagePipe ' .
            'as a parameter $_imagePipe to template' . $where
            );
        }
    }

}
