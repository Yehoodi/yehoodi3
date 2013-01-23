<?php

require_once('Zend'.DIRECTORY_SEPARATOR.'Loader.php');
Zend_Loader::loadClass('Zend_Db');

class Globals {
	private static $_db = null;
	public $DatabaseTables = array();
	
	static public function getDBConnection() {
		if (self::$_db != null){
			return self::$_db;
		}
		
		$config = new Zend_Config_Ini(Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ini', 'database');
		$params = array(
				'host' 		=> $config->dbhost,
				'port' 		=> $config->dbport,
				'username' 	=> $config->dbusername,
				'password' 	=> $config->dbpassword,
				'dbname' 	=> $config->dbname
        			);

        // Debugging? Smarty and PQP switch
/*		if (Zend_Registry::get('smartyConfig')->debugging == true) {
			$params['profiler'] = array('enabled' 	=> true,
					        			'class' 	=> 'PQP_Zend_Db_Profiler'
					        			);
		}
*/						
		self::$_db = Zend_Db::factory('Pdo_Mysql', $params);
		Zend_Db_Table::setDefaultAdapter(self::$_db);

		return self::$_db;
	}
	
} // Class Globals
