<?php

/**
 * DB Class for reading form the phpBB database
 *
 */

class PhpBB {
	
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
	public function getTopicCount() {
		$sql = $this->db->prepare("SELECT t.topic_id, t.topic_title, t.topic_poster, t.topic_time, t.topic_views, t.forum_id, t.topic_status, t.topic_first_post_id, pt.bbcode_uid FROM topics AS t
									JOIN posts AS p ON t.topic_id = p.topic_id
									JOIN posts_text AS pt ON p.post_id = pt.post_id
									INNER JOIN datyehoodi3_dev.user AS u1 ON t.topic_poster = u1.user_id
									INNER JOIN datyehoodi3_dev.user AS u2 ON p.poster_id = u2.user_id
									WHERE t.topic_poster > 0 AND p.poster_id > 0
									GROUP BY t.topic_id
									ORDER BY t.topic_id");

		$sql->execute();
		return $sql->rowCount();
	}

	public function getCommentCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM posts");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	public function getCommentCountById($id) {
		$sql = $this->db->prepare("SELECT COUNT(*)
									FROM posts p, posts_text pt
									WHERE p.post_id = pt.post_id
									AND p.topic_id = {$id};");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	public function getTopics($limit = 0,$offset = 0) {
		$sql = $this->db->prepare("SELECT t.topic_id, t.topic_title, t.topic_poster, t.topic_time, t.topic_views, t.forum_id, t.topic_status, t.topic_first_post_id, pt.bbcode_uid FROM topics AS t
									JOIN posts AS p ON t.topic_id = p.topic_id
									JOIN posts_text AS pt ON p.post_id = pt.post_id
									INNER JOIN datyehoodi3_dev.user AS u1 ON t.topic_poster = u1.user_id
									INNER JOIN datyehoodi3_dev.user AS u2 ON p.poster_id = u2.user_id
									WHERE t.topic_poster > 0 AND p.poster_id > 0
									GROUP BY t.topic_id
									ORDER BY t.topic_id LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getForums() {
		$sql = $this->db->prepare("SELECT forum_id, forum_name, forum_desc, cat_id FROM forums");

		$sql->execute();
		$result = $sql->fetch();

		return $result;
	}

	public function getPosts() {
		$sql = $this->db->prepare("SELECT p.topic_id, p.forum_id, p.poster_id, p.post_time, pt.post_text FROM posts p, posts_text pt WHERE p.post_id = pt.post_id");

		$sql->execute();
		$result = $sql->fetch();

		return $result;
	}

	public function getSinglePost($id) {
		$sql = $this->db->prepare("SELECT p.topic_id, p.forum_id, p.poster_id, p.post_time, pt.post_text FROM posts p, posts_text pt WHERE p.post_id = pt.post_id AND p.topic_id = {$id} ORDER BY p.post_time ASC;");

		$sql->execute();
		$result = $sql->fetch();

		//var_dump($sql)."\n";
		return $result;
	}
	
	public function getCommentContentsById($id,$count) {
		$sql = $this->db->prepare("SELECT p.post_id, p.post_time, p.poster_id, pt.post_text, p.poster_ip, pt.bbcode_uid 
									FROM posts p, posts_text pt
									WHERE p.post_id = pt.post_id
									AND p.topic_id = {$id}
									ORDER BY p.post_time ASC
									LIMIT {$count} OFFSET 1;");
		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getAvatars($limit = 0,$offset = 0) {
		$sql = $this->db->prepare("SELECT user_id, user_avatar FROM users WHERE user_avatar <> '' ORDER BY user_id LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getAvatarCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM users WHERE user_avatar <> ''");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

		public function getPrivateMessageCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM privmsgs");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	public function getPrivateMessages($limit = 0,$offset = 0) {
		$sql = $this->db->prepare("SELECT privmsgs_id, privmsgs_from_userid, privmsgs_to_userid, privmsgs_subject, privmsgs_date, privmsgs_ip FROM privmsgs LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getAllMessagesByUserId($user_id) {
		$sql = $this->db->prepare("SELECT `pm`.`privmsgs_id`, `pm`.`privmsgs_from_userid`, `pm`.`privmsgs_to_userid`, `pm`.`privmsgs_subject`, `pm`.`privmsgs_date`, `pm`.`privmsgs_ip`, `u`.`username` AS `recipient_user_name` 
									FROM `privmsgs` AS `pm`
 									INNER JOIN `users` AS `u` ON pm.privmsgs_to_userid = u.user_id 
 									WHERE (pm.privmsgs_to_userid in ({$user_id}) OR (pm.privmsgs_from_userid in ({$user_id}))) 
 									GROUP BY `pm`.`privmsgs_date` 
 									ORDER BY `pm`.`privmsgs_date` DESC;");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

		public function getPrivateBodyCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM privmsgs_text");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	public function getPrivateBodys($limit = 0,$offset = 0) {
		$sql = $this->db->prepare("SELECT privmsgs_text_id, privmsgs_bbcode_uid, privmsgs_text FROM privmsgs_text LIMIT {$limit} OFFSET {$offset}");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

		public function getViews($id) {
		$sql = $this->db->prepare("SELECT topic_views FROM topics WHERE topic_id = {$id}");

		$sql->execute();
		$result = $sql->fetchColumn();
		
		if(!$result) {
			return 0;
		}

		return $result;
	}
}