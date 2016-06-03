<?php

/**
 * nginx example
 * location ~* \.(png|gif|jpg|jpeg)$ {
 *    try_files $uri /create-image.php;
 * }
 *
 * apache @todo
 */

$container = require __DIR__ . '/../app/bootstrap.php';

function headerNotFound()
{
	Tracy\Debugger::enable(TRUE);
	header("HTTP/1.0 404 Not Found");
	exit;
}

if (!isset($_SERVER['REQUEST_URI']) || !preg_match('~(?P<resolution>\d+?x\d+?)-(?P<method>\d+)/(?P<name>.*)$~', $_SERVER['REQUEST_URI'], $find)) {
	headerNotFound();
}

/* @var $imageView h4kuna\ImageManager\ImageView */
$imageView = $container->getByType('h4kuna\ImageManager\ImageView');

try {
	$imageView->send($find['name'], $find['resolution'], $find['method']);
} catch (h4kuna\ImageManager\ImageManagerException $e) {
	headerNotFound();
}
