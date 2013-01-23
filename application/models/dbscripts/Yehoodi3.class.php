<?php

/**
 * DB Class for reading form the Y3 database
 *
 */
class Yehoodi3 {
	
	// Db Info. Usually this would go in a separate file...
	private $_dbhost = 'localhost';
	private $_dbname = 'datyehoodi3_dev';
	private $_user = 'webadmin';
	private $_pass = 'yehood1c0m';

	public $db;
	
	function __construct() {
		$this->db = new PDO("mysql:host=$this->_dbhost;dbname=$this->_dbname", $this->_user, $this->_pass);
	}

	/**
	 * getResources()
	 *
	 * @return array
	 */
	public function getResources($limit,$offset) {
		$sql = $this->db->prepare("SELECT rsrc_id, user_id, count_comments FROM resource WHERE is_active = 1 ORDER BY rsrc_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getResourceIds()
	 *
	 * @return array
	 */
	public function getResourceIds($limit,$offset) {
		$sql = $this->db->prepare("SELECT rsrc_id FROM resource ORDER BY rsrc_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getResourceDescriptions()
	 *
	 * @return array
	 */
	public function getResourceDescriptions($limit,$offset) {
		$sql = $this->db->prepare("SELECT rsrc_id, descrip, bbcode_uid FROM resource ORDER BY rsrc_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getCommentText()
	 *
	 * @return array
	 */
	public function getCommentText($limit,$offset) {
		$sql = $this->db->prepare("SELECT `comment`, comment_id, bbcode_uid FROM `comment` ORDER BY comment_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getCommentHistoryText()
	 *
	 * @return array
	 */
	public function getCommentHistoryText($limit,$offset) {
		$sql = $this->db->prepare("SELECT `comment`, comment_id FROM `comment_history` ORDER BY comment_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getResourceIdAndDate()
	 *
	 * @return array
	 */
	public function getResourceIdAndDate($limit,$offset) {
		$sql = $this->db->prepare("SELECT rsrc_id, rsrc_date FROM resource ORDER BY rsrc_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getResourceCommentCount()
	 *
	 * @return array
	 */
	public function getResourceCommentCount($id) {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM comment WHERE rsrc_id = {$id};");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return (int) $result;
	}

	/**
	 * getUserResourceCommentCount()
	 *
	 * @return array
	 */
	public function getUserResourceCommentCount($id) {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM resource WHERE user_id = {$id};");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return (int) $result;
	}

	/**
	 * getResourceViewCount()
	 *
	 * @return array
	 */
	public function getResourceViewCount($id) {
		$sql = $this->db->prepare("SELECT views_lifetime FROM resource_stats WHERE rsrc_id = {$id};");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		if (!$result) {
		    return 0;
		}
		
		return $result;
	}

	public function getExistingPostId($id) {
		$sql = $this->db->prepare("SELECT comment_id FROM comment WHERE comment_id = {$id};");

		$sql->execute();
		if($sql->fetchAll(PDO::FETCH_ASSOC)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getIdForUserName($name) {
		
		// this one's for Rik!
		if ($name == 'Slick Rik') {
			return 1763;
		}
		
		$sql = $this->db->prepare("SELECT user_id, user_name FROM user WHERE user_name = \"{$name}\";");

		$sql->execute();
		$result = $sql->fetchColumn();
		
		return $result;
	}

	/**
	 * getEventResources()
	 *
	 * @return array
	 */
	public function getEventResources($limit,$offset) {
		$sql = $this->db->prepare("SELECT yr.rsrc_id, yr.start_date FROM resource AS yr WHERE yr.start_date != '0000-00-00 00:00:00' LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}//getEventResources

	public function getResourceCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM resource");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getCommentCount()
	 * 
	 * Gets the number of comments in the comment table
	 *
	 * @return unknown
	 */
	public function getCommentCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM comment");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getCommentHistoryCount()
	 * 
	 * Gets the number of comment_history in the comment table
	 *
	 * @return unknown
	 */
	public function getCommentHistoryCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM comment_history");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getUserCommentCount()
	 * 
	 * Gets the number of comments made by a user
	 *
	 * @return unknown
	 */
	public function getUserCommentCount($id) {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM comment WHERE user_id = {$id}");

		$sql->execute();
		$result = $sql->fetchColumn();

		return (int) $result;
	}

	public function getResourceUrlCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM resource_url");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	public function getUrlCount($url) {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM resource_url WHERE rsrc_url = \"{$url}\"");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getResourceTitle()
	 *
	 * @return array
	 */
	public function getResourceTitle($limit,$offset) {
		$sql = $this->db->prepare("SELECT rsrc_id, title FROM resource LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getUrlsByTitle()
	 *
	 * @return array
	 */
	public function getUrlsByTitle($url) {
		$sql = $this->db->prepare("SELECT url_id, rsrc_id, rsrc_url FROM resource_url WHERE rsrc_url = \"{$url}\"");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getResourceUrl()
	 *
	 * @return array
	 */
	public function getResourceUrl($limit,$offset) {
		$sql = $this->db->prepare("SELECT url_id, rsrc_id, rsrc_url FROM resource_url ORDER BY rsrc_url LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * Builds unique Urls for 
	 * SEO of resources
	 *
	 * @param string $title
	 * @param int $id
	 * @return string unique URL
	 */
	public function generateUniqueUrl($title, $id)
	{
	    $url = strtolower($title);
	    
	    //convert html encoded things back to html in prep for the filter
	    $title = htmlspecialchars_decode($title);
	
	    $filters = array(
	        // replace & with 'and' for readability
	        '/&+/' => 'and',
	
	        // replace non-alphanumeric characters with a hyphen
	        '/[^a-z0-9]+/i' => '-',
	
	        // replace multiple hyphens with a single hyphen
	        '/-+/'          => '-'
	    );
	
	
	    // apply each replacement
	    foreach ($filters as $regex => $replacement)
	        $url = preg_replace($regex, $replacement, $url);
	
	    // restrict the length of the URL
	    $url = trim(substr($url, 0, 60));

	    // remove hyphens from the start and end of string
	    $url = trim($url, '-');
	
	    // set a default value just in case
	    if (strlen($url) == 0)
	        $url = 'post';
	
	
	    // find similar URLs
		$sql = $this->db->prepare("select rsrc_url from resource_url where rsrc_id = {$id} and rsrc_url like \"{$url}%\"");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		
		//var_dump($result);die;

		//$query = sprintf("select rsrc_url from %s where rsrc_id = %d and url like ?",
	    //                 'resource_url',
	    //                 $id);
	
	    //$query = $this->db->quote($query, $url . '%');
		//$query->execute();
	    //$result = $query->fetchColumn();
	
	
	    // if no matching URLs then return the current URL
	    if (count($result) == 0 || !in_array($url, $result))
	        return $url;
	
	    // generate a unique URL
	    $i = 2;
	    do {
	        $_url = $url . '-' . $i++;
	    } while (in_array($_url, $result));
	
	    return $_url;
	}

	/**
	 * Builds unique Urls for 
	 * SEO of resources
	 *
	 * @param string $title
	 * @param int $id
	 * @return string unique URL
	 */
	public function cleanupUniqueUrl($url, $id)
	{
	    // find similar URLs
		$sql = $this->db->prepare("select rsrc_url from resource_url where rsrc_url like \"{$url}%\"");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_COLUMN);
		
		//var_dump($result)."\n";

		// if no matching URLs then return the current URL
	    if (count($result) > 1)
	        //die('Match on' . var_dump($result));
		    // generate a unique URL
		    $i = 2;
		    do {
		        $_url = $url . '-' . $i++;
		    } while (in_array($_url, $result));
		
		    die('Match found. URL renamed to' . $_url);
		    return $_url;
			return FALSE;
			
	}

	public function processUrlUpdate($newUrl, $id)
	{
		//$this->db->beginTransaction();
		$this->db->exec("UPDATE resource_url SET rsrc_url = \"{$newUrl}\" WHERE url_id = {$id}");
	}

	/**
	 * getIndexableResourcesCount()
	 *
	 * @return array
	 */
	public function getIndexableResourcesCount() {
		//$sql = $this->db->prepare("SELECT rsrc_id, user_id, title, descrip, rsrc_date, count_comments FROM resource ORDER BY rsrc_id LIMIT {$limit} OFFSET {$offset};");
		$sql = $this->db->prepare("SELECT COUNT(*) FROM `resource` AS `r`
 INNER JOIN `category` AS `c` ON r.cat_id = c.cat_id
 INNER JOIN `resource_type` AS `rt` ON c.rsrc_type_id = rt.rsrc_type_id
 INNER JOIN `user` AS `u` ON r.user_id = u.user_id WHERE rsrc_date > 0 AND r.is_active = 1 AND r.cat_id != 32;");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}


	/**
	 * getSolrResources()
	 *
	 * @return array
	 */
	public function getSolrResources($limit,$offset) {
		$sql = $this->db->prepare("SELECT `r`.`rsrc_id` as id, `r`.`title`, `r`.`descrip`,`r`.`rsrc_date` AS date, `u`.`user_id`, `u`.`user_name`, r.bbcode_uid FROM `resource` AS `r`
 INNER JOIN `category` AS `c` ON r.cat_id = c.cat_id
 INNER JOIN `resource_type` AS `rt` ON c.rsrc_type_id = rt.rsrc_type_id
 INNER JOIN `user` AS `u` ON r.user_id = u.user_id WHERE r.rsrc_date > 0 AND r.is_active = 1 
 AND (r.cat_id NOT IN (32, 34)) LIMIT {$limit} 
OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as &$value) {
			$value['type'] = 'resource';
			$value['unique_id'] = 'r:' . $value['id'];
			$gmtDate = date('U',strtotime($value['date']));
			$value['date'] = gmdate("Y-m-d\TH:i:s\Z", $gmtDate);
		}
				
		return $result;
	}//getSolrResources()

	/**
	 * getIndexableCommentCount()
	 *
	 * @return array
	 */
	public function getIndexableCommentCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM `comment` AS `c`
 INNER JOIN `user` AS `u` ON c.user_id = u.user_id AND c.is_active = 1;");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}


	/**
	 * getSolrComments()
	 *
	 * @return array
	 */
	public function getSolrComments($limit,$offset) {
		$sql = $this->db->prepare("SELECT `c`.`comment_id` as id, `c`.`comment`, `c`.`date_created` AS date,`u`.`user_id`,`u`.`user_name`, c.bbcode_uid FROM `comment` AS `c`
INNER JOIN `user` AS `u` ON c.user_id = u.user_id WHERE c.is_active = 1 LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as &$value) {
			$value['type'] = 'comment';
			$value['unique_id'] = 'c:' . $value['id'];
			$gmtDate = date('U',strtotime($value['date']));
			$value['date'] = gmdate("Y-m-d\TH:i:s\Z", $gmtDate);
		}

		return $result;
	}//getSolrComments()

	/**
	 * getUsers()
	 *
	 * @return array
	 */
	public function getUsers() {
		$sql = $this->db->prepare("SELECT user_name, date_first_visit FROM user;");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getIndexableUserCount()
	 *
	 * @return array
	 */
	public function getIndexableUserCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM user u WHERE user_id > 0 AND u.is_active = 1;");
		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getUserCount()
	 *
	 * @return array
	 */
	public function getUserCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM user ORDER BY user_id;");

		$sql->execute();
		$result = (int) $sql->fetchColumn();
				
		return $result;
	}

	/**
	 * getSolrEvents()
	 *
	 * @return array
	 */
	public function getSolrEvents($limit,$offset) {
		$sql = $this->db->prepare("SELECT l.rsrc_id AS id, r.title, l.description, r.start_date AS date FROM location AS l
INNER JOIN resource AS r ON l.rsrc_id = r.rsrc_id
WHERE r.start_date > '0' 
	AND r.is_active = 1
	AND l.primary_location = 1 LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as &$value) {
			$value['type'] = 'event';
			$value['unique_id'] = 'e:' . $value['id'];
			$gmtDate = date('U',strtotime($value['date']));
			$value['date'] = gmdate("Y-m-d\TH:i:s\Z", $gmtDate);
		}

		return $result;
	}

	/**
	 * getEventCount()
	 *
	 * @return array
	 */
	public function getEventCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM location WHERE primary_location = 1;");

		$sql->execute();
		$result = (int) $sql->fetchColumn();
				
		return $result;
	}

	/**
	 * getUserIds()
	 *
	 * @return array
	 */
	public function getUserIds($limit, $offset) {
		$sql = $this->db->prepare("SELECT user_name, user_id FROM user ORDER BY user_name LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	public function getAllMessagesByUserIdSecondPass($user_id) {
		$sql = $this->db->prepare("SELECT `m`.`mail_id`, `m`.`user_id_from`, `m`.`user_id_to`, `m`.`mail_subject`, `m`.`mail_date`, `m`.`remote_ip`, `u`.`user_name` AS `recipient_user_name` 
									FROM `temp_mail` AS `m`
 									INNER JOIN `user` AS `u` ON m.user_id_to = u.user_id 
 									WHERE (m.user_id_to in ({$user_id})) OR (m.user_id_from in ({$user_id})) 
 									GROUP BY m.mail_date 
 									ORDER BY `m`.`mail_subject` ASC;");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	/**
	 * getLastResourceId()
	 *
	 * @return array
	 */
	public function getLastResourceId() {
		$sql = $this->db->prepare("SELECT MAX(rsrc_id) FROM resource;");

		$sql->execute();
		$result = (int) $sql->fetchColumn();
				
		return $result;
	}

	/**
	 * getLastCommentId()
	 *
	 * Gets the lastCommentId for a resource (if any)
	 * 
	 * @return int
	 */
	public function getLastCommentId($id) {
		$sql = $this->db->prepare("SELECT comment_id FROM comment WHERE rsrc_id = {$id}
									AND comment_num = (SELECT MAX(comment_num) FROM comment WHERE rsrc_id = {$id});");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}

	/**
	 * getLastCommentDate()
	 *
	 * Gets the lastCommentDate for a resource (if any)
	 * 
	 * @return int
	 */
	public function getLastCommentDate($id) {
		$sql = $this->db->prepare("SELECT date_created FROM comment WHERE rsrc_id = {$id}
									AND comment_num = (SELECT MAX(comment_num) FROM comment WHERE rsrc_id = {$id});");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}

	/**
	 * getCommentsForResource()
	 *
	 * @return array
	 */
	public function getCommentsForResource($rsrcId) {
		$sql = $this->db->prepare("SELECT c.comment_id, c.rsrc_id, c.comment_num FROM `comment` AS `c`
									INNER JOIN `user` AS `u` ON c.user_id = u.user_id 
									WHERE c.rsrc_id = {$rsrcId} ORDER BY c.comment_id;");
		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}
	
	/**
	 * getDescriptionForResource()
	 *
	 * @return array
	 */
	public function getDescriptionForResource($rsrcId) {
		$sql = $this->db->prepare("SELECT r.descrip FROM `resource` AS `r`
									WHERE r.rsrc_id = {$rsrcId};");
		$sql->execute();
		$result = $sql->fetchColumn();
				
		return $result;
	}

	/**
	 * getResourceImages()
	 *
	 * @return array
	 */
	public function getResourceImages() {
		$sql = $this->db->prepare("SELECT * FROM resource_image;");
		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getTrackingCount()
	 * 
	 * Gets the number of tracking rows in the comment_tracking table
	 *
	 * @return unknown
	 */
	public function getTrackingCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM comment_tracking");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getTrackingInfo()
	 *
	 * @return array
	 */
	public function getTrackingInfo($limit,$offset) {
		$sql = $this->db->prepare("SELECT `track_id`, rsrc_id, comment_num FROM `comment_tracking` LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getUserIdFromTrackInfo()
	 *
	 * @return array
	 */
	public function getUserIdFromTrackInfo($id,$commentNum) {
		$sql = $this->db->prepare("SELECT user_id FROM `comment` WHERE rsrc_id = {$id} AND comment_num = {$commentNum};");

		$sql->execute();
		$result = $sql->fetchColumn();
				
		return (int) $result;
	}

	/**
	 * getMessageCount()
	 * 
	 * Gets the number of messages in the mail_body table
	 *
	 * @return int
	 */
	public function getMessageCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM mail_body");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getMessageText()
	 *
	 * @return array
	 */
	public function getMessageText($limit,$offset) {
		$sql = $this->db->prepare("SELECT mail_body, mail_id, bbcode_uid FROM `mail_body` ORDER BY mail_id LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
				
		return $result;
	}

	/**
	 * getSigCount()
	 * 
	 * Gets the number of sigs that are not empty in the user_profile table
	 *
	 * @return int
	 */
	public function getSigCount() {
		$sql = $this->db->prepare("SELECT COUNT(*) FROM user_profile WHERE profile_key = 'sig'
                                    AND profile_value != '';");

		$sql->execute();
		$result = $sql->fetchColumn();

		return $result;
	}

	/**
	 * getSignatureText()
	 * 
	 * Gets the number of sigs that are not empty in the user_profile table
	 *
	 * @return int
	 */
	public function getSignatureText($limit,$offset) {
		$sql = $this->db->prepare("SELECT up.user_id, up.profile_value AS signature, up1.profile_value AS bbcode_uid FROM user_profile AS up
                                     INNER JOIN user_profile AS up1 ON up.user_id = up1.user_id
                                     WHERE up.profile_key = 'sig' 
                                     AND up.profile_value != ''
                                     AND up1.profile_key = 'bbcode_uid' LIMIT {$limit} OFFSET {$offset};");

		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}


}
