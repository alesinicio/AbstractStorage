<?php
function loadClass($class) {
	$filename = __DIR__.'/vendor/'.str_replace('\\', '/', $class).'.php';
	if (file_exists($filename)) {
		require_once $filename;
	} else {
		throw new Exception('Critical error - class '.$class.' not found. Aborting operation. '.$_SERVER['REQUEST_URI']);
	}
}
spl_autoload_register('loadClass');