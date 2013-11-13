<?php

namespace h4kuna\DI;

use Nette\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

if (defined('\Nette\Framework::VERSION_ID') || Framework::VERSION_ID < 20100) {
    if (!class_exists('Nette\DI\CompilerExtension')) {
        class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
    }

    if (!class_exists('Nette\DI\Compiler')) {
        class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
    }
}

class ImageExtension extends CompilerExtension {

    public $defaults = array(
        'maxSize' => '2000x2000',
        'domain' => NULL,
        'namespace' => array(),
        'noImage' => NULL,
        'wwwDir' => '%wwwDir%',
        'test' => FALSE, // message show in nette debug bar
        // Below properies are relative from wwwDir
        'source' => 'upload/public',
        'temp' => NULL
    );

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);

        if (!$config['namespace']) {
            throw new \h4kuna\ImageManagerException('Namespace is required');
        }

        $manager = $builder->addDefinition($this->prefix('imageManager'))
                ->setClass('h4kuna\ImageManager')
                ->setArguments(array($config['wwwDir'], '@httpRequest', $config['source'], $config['temp']));

        $engine = $builder->getDefinition('nette.latte');
        $engine->addSetup('h4kuna\Macros\Latte::install(?->compiler, ?)', array('@self', $this->prefix('@imageManager')));

        foreach ($config['namespace'] as $ns => $setup) {
            array_unshift($setup, $ns);
            $manager->addSetup('appendNs', $setup);
        }


        if ($config['maxSize']) {
            $manager->addSetup('setMaxSize', array($config['maxSize']));
        }

        if ($config['noImage']) {
            $manager->addSetup('setNoImage', array($config['noImage']));
        }

        if ($config['domain']) {
            $manager->addSetup('setDomain', array($config['domain']));
        }

        if ($config['test']) {
            $manager->addSetup('checkSetup');
        }
    }

    /**
     * @param \Nette\Configurator $configurator
     */
    public static function register(Configurator $configurator) {
        $that = new static;
        $configurator->onCompile[] = function ($config, Compiler $compiler) use ($that) {
            $compiler->addExtension('imageExtension', $that);
        };
    }

}
