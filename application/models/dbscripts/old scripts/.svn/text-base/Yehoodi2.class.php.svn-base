<?php

/**
 * DB Class for reading form the Y2 database
 *
 */
class Yehoodi2 {
	
	// Db Info. Usually this would go in a separate file...
	private $_dbhost = 'localhost';
	private $_dbname = 'datyehoodi2_import';
	private $_user = 'webadmin';
	private $_pass = 'yehood1c0m';

	public $db;
	
	function __construct() {
		$this->db = new PDO("mysql:host=$this->_dbhost;dbname=$this->_dbname", $this->_user, $this->_pass);
	}

	/**
	 * getNewsData()
	 *
	 * @return array
	 */
	public function getYehoodi2News($limit = 0,$offset = 0) {
		$result = array();
		$sql = $this->db->prepare("SELECT Author, ActiveDate, ActiveTime, Region, Header, Article, FullArticle, TopicID FROM tblNews LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getNewsCount()
	 *
	 * @return array
	 */
	public function getYehoodi2NewsCount() {
		$result = array();
		$sql = $this->db->prepare("SELECT COUNT(*) FROM tblNews");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}
	
	/**
	 * getSingleNewsDescription()
	 *
	 * @return array
	 */
	public function getSingleNewsArticle($descrip) {
		$result = array();
		$sql = $this->db->prepare("SELECT Article FROM tblNews WHERE Header = \"{$descrip}\"");

		$sql->execute();
		if ($result = $sql->fetchColumn()) {
			return $result;
		} else {
			return FALSE;
		}
				
	}	

	/**
	 * getStateById()
	 *
	 * @return array
	 */
	public function getStateById($id) {
		$result = array();
		$sql = $this->db->prepare("SELECT State FROM tblState WHERE StateID = {$id}");

		$sql->execute();
		if ($result = $sql->fetchColumn()) {
			return $result;
		} else {
			return FALSE;
		}
	} // getStateById

	/**
	 * getEventData()
	 *
	 * @return array
	 */
	public function getEventData($limit = 0,$offset = 0) {
		$result = array();
		$sql = $this->db->prepare("SELECT EventID, TypeID, EventDate, Title, Description, RegionID, City, PostBy FROM tblCalendar GROUP BY Title ORDER BY EventID LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	} //getEventData

	/**
	 * getEventCount()
	 *
	 * @return array
	 */
	public function getEventCount() {
		$result = array();
		$sql = $this->db->prepare("SELECT COUNT(DISTINCT Title) FROM tblCalendar");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}// getEventCount
	
	/**
	 * getYehoogleCount()
	 *
	 * @return int
	 */
	public function getYehoogleCount() {
		$result = array();
		$sql = $this->db->prepare("SELECT COUNT(*) FROM tblRsrc");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}//getYehoogleCount

	/**
	 * getYehoogles()
	 *
	 * @return array
	 */
	public function getYehoogles($limit = 0,$offset = 0) {
		$result = array();
		$sql = $this->db->prepare("SELECT rsrcID, rsrcName, rsrcURL, rsrcAddress1, rsrcCity, rsrcState, rsrcCountry, rsrcDescription, rsrcMisc, PostBy FROM tblRsrc LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}//getYehoogles

	/**
	 * getRsrcGroupID()
	 *
	 * @return array
	 */
	public function getRsrcGroupID($id) {
		$sql = $this->db->prepare("SELECT GroupID FROM tblJoinRsrcGrp WHERE rsrcID = {$id}");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}//getRsrcGroupID



}