<?php
/**
 * This file contains configuration for the Yehoodi 3 Application
 * 
 * @author Manu Smith
 */

/**
 * ---------------------------------
 * General application configuration
 * ---------------------------------
 */

date_default_timezone_set('America/New_York');

$lib_paths = array();
$lib_paths[] = Y3ROOT_PATH . 'application';
$lib_paths[] = Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'controllers';
$lib_paths[] = Y3ROOT_PATH . 'library';
$lib_paths[] = Y3ROOT_PATH . 'library'.DIRECTORY_SEPARATOR.'Smarty';
$inc_path = implode(PATH_SEPARATOR, $lib_paths);
set_include_path($inc_path);

/**
 * Functions Path
 */
require_once Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'Globals.php';
require_once 'common.php';