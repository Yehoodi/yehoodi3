<?php
/**
 * Registry Entries go here
 * 
 */

// Server registry 
$serverConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'server');
Zend_Registry::set('serverConfig', $serverConfig);

// Tables registry entry and config
$dbTableConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'tables');
Zend_Registry::set('dbTableConfig', $dbTableConfig);

// Smarty config and registry
$smartyConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'development');
Zend_Registry::set('smartyConfig', $smartyConfig);

// Emails
$emailConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'Email');
Zend_Registry::set('emailConfig', $emailConfig);

// Resources
$resourceConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'ResourceType');
Zend_Registry::set('resourceConfig', $resourceConfig);

// Search
$searchConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'Search');
Zend_Registry::set('searchConfig', $searchConfig);

// Images 
$imageConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'Images');
Zend_Registry::set('imageConfig', $imageConfig);

// Pagination 
$paginationConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'Pagination');
Zend_Registry::set('paginationConfig', $paginationConfig);

// Google Map API
$mapConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'GoogleMap');
Zend_Registry::set('mapConfig', $mapConfig);

// RSS files
$rssConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'rss');
Zend_Registry::set('rssConfig', $rssConfig);

// User Defaults
$userConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'UserDefaults');
Zend_Registry::set('userConfig', $userConfig);

// Smarty Templating System
$vr = new Zend_Controller_Action_Helper_ViewRenderer();
$vr->setView(new Templater());
$vr->setViewSuffix('tpl');
Zend_Controller_Action_HelperBroker::addHelper($vr);

// Database container registry entry
$DB = Globals::getDBConnection();
Zend_Registry::set('db', $DB);

//Application Logging
$logConfig = new Zend_Config_Ini('config'.DIRECTORY_SEPARATOR.'config.ini', 'Log');

if (Zend_Registry::get('serverConfig')->logging == TRUE) {
	// App log
	$logger = new Zend_Log(new Zend_Log_Writer_Stream($logConfig->logfile));
	Zend_Registry::set('logger', $logger);
	
	// Error log
	$errorLogger = new Zend_Log(new Zend_Log_Writer_Stream($logConfig->errorLogfile));
	Zend_Registry::set('errorLogger', $errorLogger);
	
	// Cache Log
	$cacheLogger = new Zend_Log(new Zend_Log_Writer_Stream($logConfig->cachelogfile));
	Zend_Registry::set('cachelogger', $cacheLogger);
	
	// Search Log
	$searchLogger = new Zend_Log(new Zend_Log_Writer_Stream($logConfig->searchlogfile));
	Zend_Registry::set('searchlogger', $searchLogger);
	
	// New User Log
	$userLogger = new Zend_Log(new Zend_Log_Writer_Stream($logConfig->newuserlogfile));
	Zend_Registry::set('userlogger', $userLogger);
}

// Application Authentication
$auth = Zend_Auth::getInstance();
$auth->setStorage(new Zend_Auth_Storage_Session());
//Zend_Registry::set('auth', $auth);

// Singelton Front Controller and configs
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
$frontController->setParam('noErrorHandler', true);
$frontController->setControllerDirectory( Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'controllers');

// Zend Auth & ACL custom manager
$frontController->registerPlugin(new CustomControllerAclManager($auth));
