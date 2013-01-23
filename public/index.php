<?php
//phpinfo();die;
//ini_set('display_errors',1);
//error_reporting(E_ALL|E_STRICT);
define("Y3ROOT_PATH", "/Users/spuds/Sites/yehoodi3/trunk/");

// PHP Quick Profiler
//require_once(Y3ROOT_PATH.'public'.DIRECTORY_SEPARATOR.'PQP'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'PhpQuickProfiler.php');
//require_once('/var/www/sites'.DIRECTORY_SEPARATOR.'PQP/classes/Console.php');
//require_once('/var/www/sites'.DIRECTORY_SEPARATOR.'PQP/classes/Profiler.php');
//require_once('/var/www/sites'.DIRECTORY_SEPARATOR.'PQP/classes/Display.php');
//require_once('/var/www/sites'.DIRECTORY_SEPARATOR.'PQP/classes/Zend/Db/Profiler.php');

require_once(Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php');

try {
	require_once('Zend/Loader/Autoloader.php');
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);
	$autoloader->suppressNotFoundWarnings(true);	// Dev only

	// Additional startup configs
	require Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'bootstrap.php';
	
	// Debugging? Smarty and PQP switch
//	if (Zend_Registry::get('smartyConfig')->debugging == true) {
//		$profiler = new PQP_Profiler();
//	}

	// Route configs
	require Y3ROOT_PATH .'application'.DIRECTORY_SEPARATOR.'routes.php';

	// Go! Go! Go!												  
	$frontController->dispatch();

} catch (Exception $exp) {
	// Houston...we have a problem...
	$contextType = 'text/html';
	header("Content-Type: $contextType; charset=utf-8");
	echo 'an unexpected error occured.';
	echo '<h2>Unexpected Exception: ' . $exp->getMessage() . '<h2><br /><pre>';
	echo $exp->getTraceAsString();
}
