<?php

/**
 * DB Class for reading form the Y3 database
 *
 */
class Utility {
	
	// Db Info. Usually this would go in a separate file...

	public $db;
	public $config;
	
	function __construct() {
    	$this->config = new Zend_Config_Ini(Y3ROOT_PATH . 'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ini', 'database');
    	
    	//Zend_Debug::dump($this->config);die;

    	$this->db = new PDO("mysql:host={$this->config->dbhost};dbname={$this->config->dbname}", $this->config->dbusername, $this->config->dbpassword);
	}

	/**
	 * getDBSize()
	 *
	 * @return array
	 */
	public function getDBSize() {
		$sql = $this->db->prepare("SELECT table_schema {$this->config->dbname}, SUM( data_length + index_length ) / 1024 / 1024 'size'
                                    FROM information_schema.TABLES GROUP BY table_schema;");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}
}
