<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/TestCase.php';

Tester\Environment::setup();

if (!file_exists(__DIR__ . '/../temp/cache')) {
	mkdir(__DIR__ . '/../temp/cache', 0777, true);
}

$configurator = new Nette\Bootstrap\Configurator;
$configurator->setDebugMode(false);
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->addConfig(__DIR__ . '/config.neon');

return $configurator->createContainer();
