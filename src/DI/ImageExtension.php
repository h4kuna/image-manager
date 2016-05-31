<?php

namespace h4kuna\ImageManager\DI;

use Nette\Configurator,
	Nette\DI as NDI;

class ImageExtension extends NDI\CompilerExtension
{

	public $defaults = array(
		'upload' => [
			'sourcePath' => '',
			'maxResolution' => '2000x2000'
		],
		'remoteSource' => [
			'domain' => NULL,
		],
		'public' => [
			'tempDir' => '',
			'urlPath' => '',
			'allowedResolutions' => [],
		],
		'noImage' => NULL
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		// imageView
		$imageView = $builder->addDefinition($this->prefix('imageView'));
		$imageView->setClass('h4kuna\ImageManager\ImageView', [$config['public']['allowedResolutions']]);

		// download
		$builder->addDefinition($this->prefix('download'))
			->setClass('h4kuna\ImageManager\Download\FileContent');

		// saver
		$saver = $builder->addDefinition($this->prefix('saver'));
		$saver->setClass('h4kuna\ImageManager\Saver', [$config['upload']['sourcePath']]);
		if ($config['upload']['maxResolution']) {
			$saver->addSetup('?->setMaxSize(?)', [$saver, $config['upload']['maxResolution']]);
		}

		// remote
		if ($config['remoteSource']['domain']) {
			$builder->addDefinition($this->prefix('remote'))
				->setClass('h4kuna\ImageManager\Source\RemoteSource', [$config['remoteSource']['domain']]);
			$imageView->addSetup('?->setRemote(?)', [$imageView, $this->prefix('@remote')]);
		}

		// local
		$builder->addDefinition($this->prefix('local'))
			->setClass('h4kuna\ImageManager\Source\LocalSource', [$config['public']['tempDir'], $config['public']['tempUrl'], $config['upload']['sourcePath']]);

		// placehold - url
		if (is_file($config['noImage'])) {
			// placehold - file @todo
			throw new \Nette\NotImplementedException();
		} elseif ($config['noImage'] !== FALSE) {
			$builder->addDefinition($this->prefix('placehold'))
				->setClass('h4kuna\ImageManager\Source\PlaceholdSource');
		}


//		$engine = $builder->getDefinition('nette.latte');
//		$engine->addSetup('h4kuna\ImageManager\Macros\Latte::install(?->compiler, ?)', array('@self', $this->prefix('@imageManager')));
	}

	/**
	 * @param \Nette\Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$that = new static;
		$configurator->onCompile[] = function ($config, Compiler $compiler) use ($that) {
			$compiler->addExtension('imageExtension', $that);
		};
	}

}
