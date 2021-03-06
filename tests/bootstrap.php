<?php

require_once __DIR__ . "/../vendor/autoload.php";
require __DIR__ . '/libs/ConfiguratorFactory.php';
require __DIR__ . '/libs/RequestFactory.php';

$tempDir = __DIR__ . '/temp';

if (!getenv('KEEP_TEMP')) {
	exec("rm -r $tempDir/*");
}

$configurator = h4kuna\ImageManager\Test\ConfiguratorFactory::create($tempDir);
$configurator->addConfig(__DIR__ . '/config/config.neon');

Tester\Environment::setup();
Tracy\Debugger::enable(FALSE);

return $configurator->createContainer();
