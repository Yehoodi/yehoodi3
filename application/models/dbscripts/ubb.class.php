<?php

/**
 * DB Class for reading form the old ubb tables database
 *
 */
class Ubb {
	
	// Db Info. Usually this would go in a separate file...
	private $_dbhost = 'localhost';
	private $_dbname = 'datyehoodi3_dev';
	private $_user = 'webadmin';
	private $_pass = 'yehood1c0m';

	public $db;
	
	function __construct() {
		$this->db = new PDO("mysql:host=$this->_dbhost;dbname=$this->_dbname", $this->_user, $this->_pass);
	}
	
	public function getUbbUsers() {
		$sql = $this->db->prepare("SELECT * FROM temp_ubb_dates;");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}
}