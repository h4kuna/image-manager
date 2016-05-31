<?php

namespace h4kuna\ImageManager\Test;

use Nette,
	Tracy;

class ConfiguratorFactory
{

	public static function create($tempDir)
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory($tempDir);
		Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT, $tempDir);
		return $configurator;
	}

}
