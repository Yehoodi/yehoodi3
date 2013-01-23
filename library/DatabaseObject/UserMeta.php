<?php
    class DatabaseObject_UserMeta
    {
        public $postCount = null;
        public $avatar = null;
        public $joinedDate = null;
        public $online = null;
        public $hometown = null;
        public $role = null;
        public $signature = null;
        
    	/**
    	 * Takes a user object and uses protected
    	 * methods to set this objects
    	 * properties for all the meta information
    	 * for the user
    	 *
    	 * @param object $db
    	 * @param int $user_id
    	 */
        public function __construct($db, $user_id = null)
        {
            if ($user_id) {
	        	$this->_db = $db;
	        	
                $this->user_id = $user_id;
                
	            $avatar_id = DatabaseObject_UserAvatar::loadAvatarId($this->_db, $this->user_id);

	            $this->avatar = new DatabaseObject_UserAvatar($this->_db);
	            $this->avatar->load($avatar_id);
	            
	            // Set other properties
	            $this->setPostCount();
	            $this->setJoinDate();
	            $this->setOnline();
	            $this->setHometown();
	            $this->setLindyRole();
	        	$this->setSignature();
            }
        }

        public function setOnlineTimeLimit($dateTime)
        {
            $this->timeLimit = $dateTime;
        }
        
        protected function setOnline()
        {
            if (!isset($this->timeLimit)) {
                // Grab the date
        		$dateTime = new DateTime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']));
        		$dateTime->modify('-5 minutes');
        		$this->timeLimit = $dateTime->format("Y-m-d H:i:s");
            }
            
            $options = array('user_id'      =>  $this->user_id,
                             'time_limit'   =>  $this->timeLimit);
            $this->online = DatabaseObject_User::checkOnline($this->_db, $options);
        }

        protected function setPostCount()
        {
        	$result = DatabaseObject_User::getPostCount($this->_db, array('user_id' => $this->user_id));
        	
        	$this->postCount = (int) $result;
        }

        protected function setJoinDate()
        {
        	$result = DatabaseObject_User::getJoinDate($this->_db, array('user_id' => $this->user_id));
        	
        	if (!$result) {
        	    return "n/a";
        	}
        	   
        	return $this->joinedDate = common::shortDate($result);
        }

        protected function setHometown()
        {
        	$options = array('user_id'     =>  $this->user_id,
        	                 'profile_key' =>  'location'
        	                 );
            $result = DatabaseObject_UserProfile::getUserProfileData($this->_db, $options);
        	
        	if (!$result) {
        	    return "";
        	}
        	   
        	return $this->hometown = $result;
        }

        protected function setLindyRole()
        {
        	$options = array('user_id'     =>  $this->user_id,
        	                 'profile_key' =>  'gender'
        	                 );
            $result = (int) DatabaseObject_UserProfile::getUserProfileData($this->_db, $options);
        	
        	switch ($result) {
        	    
        	    case 0:
        	        $role = '';
        	        break;
        	        
        	    case 1:
        	        $role = 'Lead';
        	        break;
        	        
        	    case 2:
        	        $role = 'Follow';
        	        break;
        	        
        	    default:
        	        $role = '';
        	        break;
        	}
        	//Zend_Debug::dump($result);die;
        	$this->role = $role;
        }

        /**
         * Sets the user's signature to the 
         * userMeta
         *
         */
        public function setSignature()
        {
        	$options = array('user_id'     =>  $this->user_id,
        	                 'profile_key' =>  'sig'
        	                 );
            $result = DatabaseObject_UserProfile::getUserProfileData($this->_db, $options);
        	
        	if (!$result) {
        	    return "";
        	}
        	   
        	return $this->signature = html_entity_decode($result);
        }

    }