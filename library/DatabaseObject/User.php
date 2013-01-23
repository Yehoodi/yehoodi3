<?php
    
class DatabaseObject_User extends DatabaseObject
    {

    	const USER_ACTIVE = 1;
        const USER_INACTIVE = 0;
        
        const USER_LEVEL_ADMIN = 1;
        const USER_LEVEL_MEMBER = 2;
    	
    	// TODO: This could change to do a select of user types from the config.ini
    	static $userTypes = array('member'        => 2,
                                  'administrator' => 1);

        public $profile = null;
        public $_newPassword = null;
        public $joinedDate = null;
        public $ignore = null;
        
        public $signature = null;
        public $sigRaw = null;
        public $userMeta = null;
        

        /**
         * Create the fields for the user
         * and instantiate the user->profile class
         *
         * @param object $db
         */
        public function __construct($db)
        {
            parent::__construct($db, 'user', 'user_id');

        	$this->add('user_name');
            $this->add('password');
            $this->add('email_address');
            //$this->add('count_visits',1);
            $this->add('user_type',2);
            $this->add('date_first_visit', time(), self::TYPE_TIMESTAMP);
            $this->add('date_last_active',0);
            $this->add('date_last_updated',0);
            $this->add('remote_ip',"");
            $this->add('resource_spam_check',0);
            $this->add('last_updated_time',0);
            $this->add('comment_spam_check',0);
            $this->add('post_count',0);
            $this->add('is_active',1);

            $this->profile = new Profile_User($db);
        }

        public function getDateFirstVisit()
        {
        	return date('F j, Y',$this->date_first_visit);
        }
        
        public function getLastLogin()
        {
        	if ($this->date_last_active == '0000-00-00 00:00:00') {
        	    return 'User never logged in.';
        	} else {
                return common::getRelativeTime($this->date_last_active);
        	}
        }
        
        protected function preInsert()
        {
            // If we are on the dev sandbox, we always set the password to 'yehoodi'
            if (Zend_Registry::get('serverConfig')->env == 'development') {
                $this->password = 'yehoodi';
            } else {
                $this->_newPassword = Text_Password::create(8);
                $this->password = $this->_newPassword;
            }
            
            return true;
        }

        protected function postLoad()
        {

        	$this->profile->setUserId($this->getId());
            $this->profile->load();
            
            // Get the user meta info
            $this->userMeta = new DatabaseObject_UserMeta($this->_db, $this->getId());

        	// set the formatting of the user joined date
        	$this->joinedDate = date("l F j, Y",$this->date_first_visit);
        	
        	$this->signature = html_entity_decode($this->profile->sig);

        	/**
             * User Stuff!
             */
            // Get the ignore status
        	$auth = Zend_Auth::getInstance()->getIdentity();

        	if (is_object($auth)) {
				// user ignoring this member?
				$options = array(
							'user_id'			=>	$auth->user_id,
							'ignored_user_id'	=>	$this->getId()
							);
	        	$this->ignore = DatabaseObject_UserIgnore::getIgnore($this->_db, $options);
			}
        }

        protected function postInsert()
        {
        	$this->profile->setUserId($this->getId());
            $this->profile->save(false);

            $this->sendEmail('user-register.tpl');
            return true;
        }

        protected function postUpdate()
        {
			$this->profile->save(false);
            return true;
        }

        protected function preDelete()
        {
        	$this->profile->delete();
            return true;
        }

        public function getLastVisitDate()
        {
            if ($this->date_last_updated) {
                return common::neatDateTime($this->date_last_updated);
            }
            
            return 'No previous visits';
        }
        
        /**
         * Checks if the username given is the
         * owner.
         *
         * @param string $username
         * @return bool
         */
        public function loadByUsername($username)
        {
            $username = trim($username);
            if (strlen($username) == 0)
                return false;

            $query = sprintf('select %s from %s where user_name = ?',
                             join(', ', $this->getSelectFields()),
                             $this->_table);

            $query = $this->_db->quoteInto($query, $username);

            return $this->_load($query);
        }

        /**
         * Sends an email to the user using
         * the template provided in the 
         * paramater
         *
         * @param smarty template $tpl
         */
        public function sendEmail($tpl)
        {
            $templater = new Templater();
            $templater->user = $this;
            $templater->siteUrl = Zend_Registry::get('serverConfig')->location;

            // fetch the e-mail body
            $body = $templater->render('email/' . $tpl);

            // extract the subject from the first line
            list($subject, $body) = preg_split('/\r|\n/', $body, 2);

            // now set up and send the e-mail
            $mail = new Zend_Mail();

            // set the to address and the user's full name in the 'to' line
            $mail->addTo($this->email_address,
                         trim($this->user_name));

            // get the admin 'from' details from the config
            $mail->setFrom(Zend_Registry::get('emailConfig')->fromEmail, Zend_Registry::get('emailConfig')->fromName);

            // set the subject and body and send the mail
            $mail->setSubject(trim($subject));
            $mail->setBodyText(trim($body));
            
            // If we are on a dev sandbox, skip sending mail for now.
            if (Zend_Registry::get('serverConfig')->env != 'development') {
                $mail->send();
            }
        }

        /**
         * Creates a new identity class
         * for a user
         *
         * @return identity object
         */
        public function createAuthIdentity()
        {
            $identity = new stdClass;
            $identity->user_id = $this->getId();
            $identity->user_name = $this->user_name;
            $identity->user_type = $this->user_type;
            $identity->first_name = $this->profile->first_name;
            $identity->last_name = $this->profile->last_name;
            $identity->email_address = $this->email_address;
            $identity->notify_by_email = $this->profile->notify_by_email;
            $identity->latitude = $this->profile->latitude;
            $identity->longitude = $this->profile->longitude;
            $identity->unit = $this->profile->unit;
            $identity->distance = $this->profile->distance;
            $identity->location = $this->profile->location;
            $identity->date_last_updated = $this->date_last_updated;
            
            $identity->last_visit = common::getRelativeTime($this->date_last_updated);

			// Moderator?
            $identity->mod = $this->profile->mod;
            
            return $identity;
        }

        /**
         * Runs after a successful login
         * and logs the user.
         *
         */
        public function loginSuccess()
        {
            // Set up new date time and make sure that the 
            // user doesn't get MORE than 2 months of results
            $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
            $today = $dateTime->format("Y-m-d H:i:s");

            $dateTime->modify('-5 months');
            $oldDate = $dateTime->format("Y-m-d H:i:s");
            
			// if their last login time is older than six months ago
			// then push it up to three months to keep from pulling
			// way too many results
            if($this->date_last_active < $oldDate ) {
            	$this->date_last_updated = $oldDate;
			} else {
            	$this->date_last_updated = $this->date_last_active;
			}
			
            $this->date_last_active = $today;
            
            // store the user's IP address
            $this->remote_ip = ip2long($_SERVER['REMOTE_ADDR']);
			
            unset($this->profile->new_password);
            unset($this->profile->new_password_ts);
            unset($this->profile->new_password_key);
            $this->save();

            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
	            $message = sprintf('Successful login attempt from %s user %s',
	                               $_SERVER['REMOTE_ADDR'],
	                               $this->user_name);
	
	            $logger = Zend_Registry::get('logger');
	            //Zend_Debug::dump($logger);die;
	            $logger->notice($message);
            }
        }
        
        /**
         * Runs is there is a login failure
         *
         * @param string $username
         * @param object containing Zend_Auth_Result $code
         */
        public static function LoginFailure($username, $code = '')
        {
            switch ($code) {
                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    $reason = 'Unknown username';
                    break;
                case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
                    $reason = 'Multiple users found with this username';
                    break;
                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    $reason = 'Invalid password';
                    break;
                default:
                    $reason = '';
            }

            $message = sprintf('Failed login attempt from %s user %s',
                               $_SERVER['REMOTE_ADDR'],
                               $username);

            if (strlen($reason) > 0)
                $message .= sprintf(' (%s)', $reason);

            $logger = Zend_Registry::get('logger');
            $logger->warn($message);
        }

        /**
         * Generates a new password for the user and
         * emails it to them
         *
         * @return bool
         */
        public function fetchPassword()
        {
            if (!$this->isSaved())
                return false;

            // generate new password properties
            $this->_newPassword = Text_Password::create(8);
            $this->profile->new_password = md5($this->_newPassword);
            $this->profile->new_password_ts = time();
            $this->profile->new_password_key = md5(uniqid() .
                                                   $this->getId() .
                                                   $this->_newPassword);

            // save new password to profile and send e-mail
            $this->profile->save();
            $this->sendEmail('user-fetch-password.tpl');

            return true;
        }

        /**
         * After sending a new password to the user,
         * this will check the validity of the
         * password and save it to the user.
         *
         * @param string $key
         * @return bool
         */
        public function confirmNewPassword($key)
        {
            // check that valid password reset data is set
            if (!isset($this->profile->new_password)
                || !isset($this->profile->new_password_ts)
                || !isset($this->profile->new_password_key)) {

                return false;
            }

            // check if the password is being confirm within a day
            if (time() - $this->profile->new_password_ts > 86400)
                return false;

            // check that the key is correct
            if ($this->profile->new_password_key != $key)
                return false;

            // everything is valid, now update the account to use the new password

            // bypass the local setter as new_password is already an md5
            parent::__set('password', $this->profile->new_password);

            unset($this->profile->new_password);
            unset($this->profile->new_password_ts);
            unset($this->profile->new_password_key);

            // finally, save the updated user record and the updated profile
            return $this->save();
        }

        /**
         * Checks for duplicate usernames.
         * If found, a new user must choose 
         * a different user name.
         *
         * @param string $username
         * @return bool
         */
        public function usernameExists($username)
        {
            $query = sprintf('select count(*) as num from %s where user_name = ?',
                             $this->_table);

            $result = $this->_db->fetchOne($query, $username);

            return $result > 0;
        }

        /**
         * Checks for duplicate email addresses.
         * If found, a user must use a different one
         *
         * @param email string $emailAddress
         * @return bool
         */
        public function emailExists($emailAddress)
        {
            $query = sprintf('select count(*) as num from %s where email_address = ?',
                             $this->_table);

            $result = $this->_db->fetchOne($query, $emailAddress);

            return $result > 0;
        }

        /**
         * Checks if the user has the dirty
         * word filter on
         *
         * @param object database
         * @param array options
         * @return "off" or nothing
         */
        public static function checkDirtyFilter($db, $id)
        {
            $query = sprintf('SELECT %s FROM %s WHERE user_id = (?) AND profile_key = \'filter_dirty\';',
                             'profile_value',
                             'user_profile');

            $result = $db->fetchOne($query, $id);
			//Zend_Debug::dump($query);die;

			return $result;
        }

        /**
         * Checks for username with valid charactors
         *
         * @param string $username
         * @return bool
         */
        public static function IsValidUsername($username)
        {
            $validator = new Zend_Validate_Alnum(TRUE);
            return $validator->isValid($username);
        }

        /**
         * Generic setter
         *
         * @param string $name
         * @param int $value
         * @return bool
         */
        public function __set($name, $value)
        {
            switch ($name) {
                case 'password':
                    $value = md5($value);
                    break;

                case 'user_type':
                    if (!array_key_exists($value, self::$userTypes))
                        //$value = 'member';
                        //$value = 2;
                    break;
            }

            return parent::__set($name, $value);
        }

        /**
         * 
         * User_name for a user Id
         * (ACTIVE or INACTIVE)
         *
         * @param int $userId
         * @return username string
         */
        public static function getUserNameById($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('user_name')
							);

            // set the offset, limit, and ordering of results
            if (!empty($options['limit'])) {
                $select->limit($options['limit'], $options['offset']);
            }

            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            //Zend_Debug::dump($select->__toString());
            return $db->fetchOne($select);
        }

        /**
         * Gets the users who are online within
         * the number of seconds specified in the 
         * options['time_limit']
         *
         * @param object $db
         * @param array $options
         * @return array
         */
        public static function getOnlineUsers($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('u.user_id',
            					  'u.user_name')
							);

			if(!isset(Zend_Auth::getInstance()->getIdentity()->mod)) {
        		$subSelect = $db->select();
				
				$subSelect->from('user_profile', 
            					array('user_id')
							)
        			 ->where("profile_key = 'user_invisible'"
        			 );

        		//Zend_Debug::dump($subSelect->__toString());die;
	            $invisibleUserIds = $db->fetchAll($subSelect);
	            
	            if(count($invisibleUserIds) > 0) {
	            	$select->where('u.user_id NOT IN (?)', $invisibleUserIds);
	            }
			}

			if ($options['time_limit'] > 0) {
                $select->where('last_updated_time > ?', $options['time_limit']);
            }

            // set the offset, limit, and ordering of results
            if (!empty($options['limit'])) {
                $select->limit($options['limit'], $options['offset']);
            }

            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }

        /**
         * Gets the count of users who are online within
         * the number of seconds specified in the 
         * options['time_limit']
         *
         * @param object $db
         * @param array $options
         * @return array
         */
        public static function getOnlineUsersCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null,'count(*)'
							);

			if ($options['time_limit'] > 0) {
                $select->where('last_updated_time > ?', $options['time_limit']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Gets the user's post_count
         *
         * @param object $db
         * @param user_id $id
         * @return int
         */
        public static function getPostCount($db, $id)
        {
            $select = self::_getBaseQuery($db, array('user_id'  =>  $id));
            $select->from(null,'post_count'
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Gets the user's joined date
         *
         * @param object $db
         * @param user_id $id
         * @return string
         */
        public static function getJoinDate($db, $id)
        {
            $select = self::_getBaseQuery($db, array('user_id'  =>  $id));
            $select->from(null,'date_first_visit'
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Gets the users who are online within
         * the number of seconds specified in the 
         * options['time_limit']
         *
         * @param object $db
         * @param array $options
         * @return array
         */
        public static function checkOnline($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('u.user_id')
							);
			if(!isset(Zend_Auth::getInstance()->getIdentity()->mod)) {
        		$subSelect = $db->select();
				
				$subSelect->from('user_profile', 
            					array('user_id')
							)
        			 ->where("profile_key = 'user_invisible'"
        			 );

        		//Zend_Debug::dump($subSelect->__toString());die;
	            $invisibleUserIds = $db->fetchAll($subSelect);
	            
	            if(count($invisibleUserIds) > 0) {
	            	$select->where('u.user_id NOT IN (?)', $invisibleUserIds);
	            }
			}

			if ($options['time_limit'] > 0) {
                $select->where('last_updated_time > ?', $options['time_limit']);
            }

            //Zend_Debug::dump($options);
            //Zend_Debug::dump($select->__toString());die;
            if (!$db->fetchOne($select)) {
                return false;
            }
            
            return true;
        }

        /**
         * 
         * Gets the user id for a given
         * user_name string
         *
         * @param string $user_name
         * @return user_id int
         */
        public static function getUserIdByName($db, $options)
        {
            // initialize the options
            $defaults = array(
                'offset' => 0,
                'limit'  => 1
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('user_id')
							)
					->where('user_name = (?)', $options['user_name']
					);

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0) {
                $select->limit($options['limit'], $options['offset']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * 
         * Gets the user id and user_name 
         * of the newest member of Yehoodi
         *
         * @param string $user_name
         * @return user_id int
         */
        public static function getNewestUser($db, $options)
        {
            // initialize the options
            $defaults = array(
                'offset' => 0,
                'limit'  => 1
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('u.user_id',
            					  'u.user_name')
					);

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
                $select->limit($options['limit'], $options['offset']);

            $select->order('u.date_first_visit DESC');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }

        /**
         * Static function:
         * Returns a list of users based
         * on the $options filter
         *
         * @param database object $db
         * @param array $options
         * @return object
         */
        public static function GetUsers($db, $options = array())
        {
            // initialize the options
            $defaults = array(
                'offset' => 0,
                'limit'  => 0,
                'order'  => 'u.user_name'
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_GetBaseQuery($db, $options);

            // set the fields to select
            $select->from(null, 'u.*');

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
                $select->limit($options['limit'], $options['offset']);

            $select->order($options['order']);

            // fetch user data from database
            //Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);

            // turn data into array of DatabaseObject_User objects
            $users = parent::BuildMultiple($db, __CLASS__, $data);

            if (count($users) == 0)
                return $users;

            $user_ids = array_keys($users);

            // load the profile data for loaded posts
            $profiles = Profile::BuildMultiple($db,
                                               'Profile_User',
                                               array('user_id' => $user_ids));

            foreach ($users as $user_id => $user) {
                if (array_key_exists($user_id, $profiles)
                        && $profiles[$user_id] instanceof Profile_User) {

                    $users[$user_id]->profile = $profiles[$user_id];
                }
                else {
                    $users[$user_id]->profile->setUserId($user_id);
                }
            }

            return $users;
        }

        /**
         * Static function:
         * Returns all usernames
         * on the $options filter
         *
         * @param database object $db
         * @param array $options
         * @return array
         */
        public static function getAllUserNames($db, $options = array())
        {
            // initialize the options
            $defaults = array(
                'offset' => 0,
                'limit'  => 0,
                'order'  => 'u.user_name'
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_GetBaseQuery($db, $options);

            // set the fields to select
            $select->from(null, array('u.user_id','user_name'));

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
                $select->limit($options['limit'], $options['offset']);

            $select->order($options['order']);

            // fetch user data from database
            //Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);

            return $data;
        }

        /**
         * Returns all the moderator ids of the site
         *
         * @param object $db
         * @param array $options
         * @return array
         */
        public static function getAllModerators($db, $options = array())
        {
            $select = $db->select();
            $select->from(array('up' => 'user_profile'), 'up.user_id');

            $select->where('up.profile_key = ? ', 'mod');
            
            if ($options['limit'] > 0)
                $select->limit($options['limit'], $options['offset']);

            $select->order($options['order']);

            // fetch user data from database
            //Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);

            return $data;
        }

        /**
         * Static function:
         * Returns the number of ALL users
         * (ACTIVE or INACTIVE)
         *
         * @param database object $db
         * @param array $options
         * @return int
         */
        public static function GetUsersCount($db, $options)
        {
            $select = self::_GetBaseQuery($db, $options);
            $select->from(null, 'count(*)');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        public static function getBirthdays($db, $options)
        {
            // create a query that selects from the users table
            $select = $db->select();
            $select->from(array('up' => 'user_profile'), array());

            $select->from(null, 'up.user_id',
            					'up.profile_value'
            			)
            	   ->join(array('u' => 'user'),
            			  'up.user_id = u.user_id',
            			   		array('user_name')
            			 )
	            	->where("up.profile_key = 'birthdate' AND SUBSTRING(up.profile_value,6,5) = ? ", $options['birth_date']
	            		);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }

        /**
         * Base query for many other queries
         * this class uses
         *
         * @param database object $db
         * @param array $options
         * @return db select object
         */
        private static function _GetBaseQuery($db, $options)
        {
            // initialize the options
            $defaults = array('user_id' => array());

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // create a query that selects from the users table
            $select = $db->select();
            $select->from(array('u' => 'user'), array());


            // filter results on specified user ids (if any)
            if (!empty($options['user_id']))
                $select->where('u.user_id in (?)', $options['user_id']);

            if (!empty($options['ignored_user_id']))
                $select->where('u.user_id in (?)', $options['ignored_user_id']);

            if (!empty($options['is_active']))
                $select->where('u.is_active = ?', $options['is_active']);

            return $select;
        }
        
		/**
		 * Returns the users last IP address
		 *
		 * @return string
		 */
        public function getIPAddress()
		{
			return long2ip($this->remote_ip);
		}
        
		/**
		 * Stores a cookie for autoLogin
		 *
		 */
		public function setAutologinCookie()
		{
			$cookieName = Zend_Registry::get('serverConfig')->loginCookie;

			// scramble up the md5 in the cookie
			$split = str_split(strrev($this->password), 8);
			$key = $split[3].$split[1].$split[2].$split[0];
			$cookie_time = (3600 * 24 * 30); // 30 days

			setcookie ($cookieName, 'usr='.$this->user_name.'&hash='.$key, time() + $cookie_time, '/','yehoodi.com');
		}

		/**
		 * Removes cookie for autoLogin
		 *
		 */
		public function deleteAutologinCookie()
		{
			$cookieName = Zend_Registry::get('serverConfig')->loginCookie;
			
			// exact same cookie except for an expired date to clear it (firefox)
			$split = str_split(strrev($this->password), 8);
			$key = $split[3].$split[1].$split[2].$split[0];
			$cookie_time = (-3600); // time in the past to remove cookie

			setcookie ($cookieName, 'usr='.$this->user_name.'&hash='.$key, time() + $cookie_time, '/','yehoodi.com');
		}

		/**
		 * Increases the post_count for a user
		 *
		 * @param database object $db
		 * @param int $user_id
		 */
        public static function addPostCount($db, $user_id)
		{
    		// update the db counter
    		$db->query("UPDATE user SET post_count = post_count + 1 WHERE user_id = ?", $user_id);
		}
    }