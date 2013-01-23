<?php

/**
 * DB Class for reading form the Yehoodi2 user base
 *
 */
class Y2Users {
	
	// Db Info. Usually this would go in a separate file...
	private $_dbhost = 'localhost';
	private $_dbname = 'datphpbb_import';
	private $_user = 'webadmin';
	private $_pass = 'yehood1c0m';

	public $db;
	
	function __construct() {
		$this->db = new PDO("mysql:host=$this->_dbhost;dbname=$this->_dbname", $this->_user, $this->_pass);
	}

	/**
	 * getTopics()
	 *
	 * @return array
	 */
	public function getUserInfo($userList) {
		$users = implode(',',$userList);
		$sql = $this->db->prepare("SELECT user_id, 
									username AS user_name, 
									user_regdate, 
									user_password, 
									user_email, 
									user_website AS website, 
									user_interests AS interests, 
									user_sig AS sig, 
									user_viewemail AS utilize_email, 
									user_notify AS notify_by_email, 
									-- user_notify_pm AS notify_by_pm, 
									user_avatar AS avatar, 
									user_gender AS gender, 
									user_sig_bbcode_uid AS bbcode_uid,
									user_birthday AS birthdate,
									user_lastvisit,
									1 AS filter_dirty 
								FROM users 
								WHERE user_id IN ({$users})");

		//echo $users;die;
		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	/**
	 * getUserCount()
	 *
	 * @return array
	 */
	public function getUserCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) 
									FROM users 
									WHERE user_id > 0
									AND user_lastvisit > 0");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	public function getActiveUsers()
	{
		// Get active posts users
		$sql = $this->db->prepare("SELECT u.user_id FROM users AS u
									 INNER JOIN posts AS p ON u.user_id = p.poster_id
									 WHERE u.user_id > 0
									 AND u.user_lastvisit > 0
									 GROUP BY u.user_id;");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		$postsArray = array();
		
		foreach($result as $key => $value) {
			$postsArray[] = $value['user_id'];
		}
		
		// Get active topics users
		$sql = $this->db->prepare("SELECT u.user_id FROM users AS u
									 INNER JOIN topics AS t ON u.user_id = t.topic_poster
									 WHERE u.user_id > 0
									 AND u.user_lastvisit > 0
									 GROUP BY u.user_id;");

		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		$topicsArray = array();
		
		foreach($result as $key => $value) {
			$topicsArray[] = $value['user_id'];
		}
		
		// Get active privmsgs
		$sql = $this->db->prepare("SELECT u.user_id FROM users AS u
									 INNER JOIN privmsgs AS pm ON (u.user_id = pm.privmsgs_from_userid OR u.user_id = pm.privmsgs_to_userid)
									 WHERE u.user_id > 0
									 AND u.user_lastvisit > 0
									 GROUP BY u.user_id;");

		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		$pmArray = array();
		
		foreach($result as $key => $value) {
			$pmArray[] = $value['user_id'];
		}
		
		// Merge the three resulting arrays
		$merge1 = array_merge($postsArray,$topicsArray);
		$finalArray = array_merge($merge1, $pmArray);
		
		// Return the final array
		return $finalArray;
	}
}
