<?php

namespace h4kuna\ImageManager\DI;

use Nette\DI as NDI;

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
			'useAbsolutePath' => FALSE,
			'allowedResolutions' => [],
		],
		'shortcuts' => [],
		'noImage' => NULL // @todo
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
		$saver->setClass('h4kuna\ImageManager\Saver');
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
		$local = $builder->addDefinition($this->prefix('local'))
			->setClass('h4kuna\ImageManager\Source\LocalSource');
		if($config['public']['useAbsolutePath']) {
			$local->addSetup('?->enableAbsoluteUrl()', ['@self']);
		}

		// path
		$builder->addDefinition($this->prefix('path'))
			->setClass('h4kuna\ImageManager\Path', [$config['public']['tempDir'], $config['public']['tempUrl'], $config['upload']['sourcePath']]);

		// placehold - url
		if (is_file($config['noImage'])) {
			// placehold - file @todo
			throw new \Nette\NotImplementedException();
		} elseif ($config['noImage'] !== FALSE) {
			$builder->addDefinition($this->prefix('placehold'))
				->setClass('h4kuna\ImageManager\Source\PlaceholdSource');
		}

		$builder->getDefinition('latte.latteFactory')
			->addSetup('addFilter', ['getH4kunaImageView', new NDI\Statement('function () { return ?;}', [$imageView])])
			->addSetup('h4kuna\ImageManager\Template\LatteMacro::install(?->getCompiler(), ?)', ['@self', $config['shortcuts']]);
	}

}
