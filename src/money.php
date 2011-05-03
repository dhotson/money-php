<?php

spl_autoload_register(function($class) {
	$parts = explode('\\', $class);

	$path = __DIR__ . '/' . implode('/', $parts) . '.php';

	if (!file_exists($path))
		return false;

	require_once $path;
});
