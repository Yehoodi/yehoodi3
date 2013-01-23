<?php
    class DatabaseObject_Mail extends DatabaseObject
    {

        //public $meta = null;
        public $mailBody;
        public $author;
        public $withUser;
        public $neatMailDate = null;
        public $neatMailDateTime = null;
        public $mailStatus;
        public $mailThreadCount;

        public $userMeta = null;
        protected $dateTime;
        
        const STATUS_DRAFT = 0;
        const STATUS_LIVE = 1;
        
        const MAIL_READ = 1;
        const MAIL_UNREAD = 2;
        const MAIL_NEW = 4;
        
    	/**
    	 * Constructor:
    	 * 
    	 * Defines fields in the db
    	 * Instantiates a DateTime class
    	 * 
    	 * @param object $db
    	 */
        public function __construct($db)
        {
            parent::__construct($db, 'mail', 'mail_id');

            // Set up new date time
            $this->dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));

            // These are required
            $this->add('thread_id');
            $this->add('user_id_from');
            $this->add('user_id_to');
            $this->add('user_from_deleted',0);
            $this->add('user_to_deleted',0);
            $this->add('mail_subject');
            $this->add('mail_date', $this->dateTime->format("Y-m-d H:i:s"));
            $this->add('remote_ip');
            
            // Instantiate a Mail_Body object for loading the mail for the mail ids
            $this->mailBody = new DatabaseObject_MailBody($db);
        }

        protected function preInsert()
        {
        	return true;
        }

        protected function postLoad()
        {
        	// get the user information
	        $identity = Zend_Auth::getInstance()->getIdentity();
	        
            // Load the mail object associated with this id
        	$this->mailBody->load($this->getId());
        	
        	// author is for the From: line on the message pages
        	$this->author = DatabaseObject_User::getUserNameById($this->_db, array('user_id' => $this->user_id_from));
        	
        	// Load this object's mail status
        	// I do this if because if we don't get a thread_id then we cannot look up the mail status
        	// The main mail page requires the mailStatus but the message page doesn't need it
        	if ($this->thread_id) {
	        	$this->mailStatus = DatabaseObject_MailStatus::getMailStatus($this->_db,array('thread_id' 	=>	$this->thread_id,
			        																			'user_id'	=>	$identity->user_id
			        																			));
			    $this->mailThreadCount = self::getMailThreadCount($this->_db, array('thread_id'	=>	$this->thread_id));
        	}

        	// set the formatting of the date and add to the mail object
        	$this->neatMailDate = common::neatDate($this->mail_date);
        	$this->neatMailDateTime = common::neatDateTime($this->mail_date);
        	
        	// Who is this conversation with? Yes, this is kludegy...
        	// I am also getting the user's avatar
			if ($identity->user_id == $this->user_id_to) {
				// Get the user meta info
                $this->userMeta = new DatabaseObject_UserMeta($this->_db, $this->user_id_from);
				$this->withUser = DatabaseObject_User::getUserNameById($this->_db, array('user_id' => $this->user_id_from));
			} else {
				// Get the user meta info
                $this->userMeta = new DatabaseObject_UserMeta($this->_db, $this->user_id_from);
				$this->withUser = DatabaseObject_User::getUserNameById($this->_db, array('user_id' => $this->user_id_to));
			}
        }

        protected function postInsert()
        {
        	return true;
        }

        protected function postUpdate()
        {
        	return true;
        }

        protected function preDelete()
        {
        	return true;
        }

        /**
         * Gets all user mail based
         * on the $options array
         *
         * @param object $db
         * @param array $options
         * @return array of mail objects
         */
        public static function getAllMail($db, $options)
        {
	        	// initialize the options
	        	$defaults = array(
	        		'offset'	=> 0,
	        		'limit'		=> 0,
	        		'order'		=> ''
	        	);
	        	
	            foreach ($defaults as $k => $v) {
	                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
	            }
	            
	            // instantiate a Zend select object
	            $select = $db->select();
	
	            // define the table to pull from
	            $select->from(array('m' => $db->select()->from('mail')->order('mail_id DESC')), array());

	            // set the fields to select
	            $select->from(null, 
	            				array(  'm.mail_id',
										'm.thread_id',
										'm.user_id_from',
										'm.user_id_to',
										'm.mail_subject',
										'm.mail_date',
										'm.remote_ip'
									)
								)
	            	   ->join(array('u1' => 'user'),
	            			  'm.user_id_to = u1.user_id',
	            			   		array('')
	            			 )
	            	   ->join(array('u2' => 'user'),
	            			  'm.user_id_from = u2.user_id',
	            			   		array('')
	            			 )
						->where('(m.user_id_to in (?) AND m.user_to_deleted = 0', $options['user_id_to'])
	            		->orWhere('m.user_id_from in (?) AND m.user_from_deleted = 0)', $options['user_id_to'])
	            		->group('m.thread_id');
						//->where('m.thread_id not in (SELECT thread_id FROM mail_deleted WHERE user_id = ?)', $options['user_id_to'])
	            
	            // set the offset, limit, and ordering of results
	            if ($options['limit'] > 0)
	            	$select->limit($options['limit'], $options['offset']);
	            	
	            $select->order($options['order']);
	            
	            // fetch post data from the db
	            //Zend_Debug::dump($select->__toString());die;
	            $data = $db->fetchAll($select);
	            
	            // This foreach takes the data and moves the recipient to the 'user_id_from'
	            // field for all results so the mail screen only shows the other person's
	            // avatar.
	            foreach ($data as &$value) {
	                $from = $value['user_id_from'];
	                $to = $value['user_id_to'];
	                
	                if ($from == Zend_Auth::getInstance()->getIdentity()->user_id) {
	                    $value['user_id_from'] = $value['user_id_to'];
	                }
	            }
	
	            // turn data into array of DatabaseObject_Mail objects
	            $mail = self::BuildMultiple($db, __CLASS__, $data);
	            $mail_ids = array_keys($mail);
	            
	            if(count($mail_ids) == 0)
	            	return array();
	            
            return $mail;
        }
        
        /**
         * Gets all user mail based
         * from a thread id
         *
         * @param object $db
         * @param array $options
         * @return array of mail objects
         */
        public static function getMailThread($db, $options)
        {
        	// get the user information
	        $identity = Zend_Auth::getInstance()->getIdentity();

	        // initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> 'm.mail_date ASC'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'm.mail_id',
									'm.mail_subject',
									'm.mail_date',
									'm.user_id_to',
									'm.user_id_from',
									'm.remote_ip',
									'mb.mail_id',
									'mb.mail_body'
								)
							)
            	   ->join(array('mb' => 'mail_body'),
            			  'mb.mail_id = m.mail_id',
            			   		array(''))
            	   ->where('(m.user_id_to in (?) AND m.user_to_deleted = 0) OR (m.user_id_from in (?) AND m.user_from_deleted = 0)', $identity->user_id
            			 );
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);
            

	        // Check the mail_status table
            $query = sprintf(
                'SELECT id, mail_status FROM mail_status WHERE thread_id = %d AND user_id = %d;',
                $options['thread_id'],
                $identity->user_id
            );

            //Zend_Debug::dump($query);die;
            $statusId = $db->fetchAll($query);
            
            //Zend_Debug::dump($statusId);die;
            // Kill the entry in mail_status because the user is reading the thread.
            foreach ($statusId as $value) {
	            if ($value['mail_status'] == self::MAIL_NEW || self::MAIL_UNREAD ) {
		            $status = new DatabaseObject_MailStatus($db);
		            $status->load($value['id']);
		            $status->delete();
	            }
            }
            // turn data into array of DatabaseObject_Mail objects
            $mailMessage = self::BuildMultiple($db, __CLASS__, $data);
            $mailMessageIds = array_keys($mailMessage);
            
            if(count($mailMessageIds) == 0)
            	return array();
            	
            return $mailMessage;
        } //getMailThread()
        
        /**
         * Gets the count of messages
         * from a thread id
         *
         * @param object $db
         * @param array $options
         * @return int count of threads
         */
        public static function getMailThreadCount($db, $options)
        {
        	// get the user information
	        $identity = Zend_Auth::getInstance()->getIdentity();

            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'COUNT(*)'
								)
							)
            	   ->join(array('mb' => 'mail_body'),
            			  'mb.mail_id = m.mail_id',
            			   		array(''))
            	   ->where('(m.user_id_to in (?) AND m.user_to_deleted = 0) OR (m.user_id_from in (?) AND m.user_from_deleted = 0)', $identity->user_id)
            	   ->where('m.thread_id in (?)', $options['thread_id']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            return $result;
        }
        
        /**
         * Get the count for pagination, etc...
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getAllMailCount($db, $options)
        {
            //$select = self::_getBaseQuery($db, $options);

            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('m' => 'mail'), array());

            $select->from(null, 
            				'count(*)'
							)
            	   ->join(array('u1' => 'user'),
            			  'm.user_id_to = u1.user_id',
            			   		array('')
            			 )
            	   ->join(array('u2' => 'user'),
            			  'm.user_id_from = u2.user_id',
            			   		array('')
            			 )
            		->where('(m.user_id_to in (?) AND m.user_to_deleted != 1)', $options['user_id_to'])
            		->orWhere('(m.user_id_from in (?) AND m.user_from_deleted != 1)', $options['user_id_to'])
            		->group('m.mail_subject');

            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);
            
            //Zend_Debug::dump($result);die;
            return count($result);
        } // getAllMailCount

        /**
         * Get the next mail count number
         * for insert into db
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getNextMailCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)'
            			);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select) + 1;
        }
        
        /**
         * Get the subject of the thread
         * by the thread_id
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getMailSubjectByThreadId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'm.mail_subject',
								)
							)
					->distinct();

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Get the subject of the mail by
         * the mail_id
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getSubjectById($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'm.mail_subject',
								)
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getSubjectById

        /**
         * Get the threadId of the mail by
         * the mail_id
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getThreadId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'm.thread_id',
								)
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Get count of all sent mail
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getSentMailCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'm.mail_id',
									'm.user_id_from',
								)
							)
            		->where('m.user_id_from in (?) OR m.user_id_to in (?)', $options['user_id'])
            		->group('m.mail_subject');

            //Zend_Debug::dump($select->__toString());die;
            $results = $db->fetchPairs($select);
            
            $matchSet = array_keys($results,$options['user_id']);

            //Zend_Debug::dump(count($matchSet));die;
            return count($matchSet);
        }


        /**
         * Get all sent mail
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getAllSentMail($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'm.mail_id',
									'm.user_id_from',
								)
							)
            		->where('(m.user_id_from in (?) AND m.user_from_deleted = 0) OR (m.user_id_to in (?) AND m.user_to_deleted = 0)', $options['user_id'])
            		->group('m.mail_subject');

            //Zend_Debug::dump($select->__toString());die;
            $results = $db->fetchPairs($select);

            
            //Zend_Debug::dump($results);die;
            //Zend_Debug::dump(array_keys($results,$options['user_id']));die;
            
            // Did we get back any keys for sent mail?
            if($matchSet = array_keys($results,$options['user_id'])) {
	            
	            $select = self::_getBaseQuery($db, $options);
	            $select->from(null, 
	            				array(  'm.mail_id',
										'm.thread_id',
										'm.mail_subject',
										'm.mail_date',
										'm.user_id_from',
										'm.user_id_to',
										'm.remote_ip',
									)
								)
	            		->where('m.mail_id in (?)', $matchSet)
	            		->order($options['order']);
	
	            // set the offset, limit, and ordering of results
	            if ($options['limit'] > 0) {
	            	$select->limit($options['limit'], $options['offset']);
	            }
	
	            //Zend_Debug::dump($select->__toString());die;
	            $data = $db->fetchAll($select);
	            
	            // turn data into array of DatabaseObject_Mail objects
	            $mail = self::BuildMultiple($db, __CLASS__, $data);
	            $mail_ids = array_keys($mail);
	            
            }
	            if(count($mail_ids) == 0)
	            	return array();
		            
	            return $mail;
        }


        /**
         * Get the next available threadId
         * for insert into db
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getNextThreadId($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 1,
        		'order'		=> 'thread_id DESC'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'thread_id'
            			);

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select) + 1;
        }

        
        /**
         * Get the other person's username
         * in a thread mail conversation
         *
         * @param db object $db
         * @param int $threadId
         * @return string user_name
         */
        public static function getUserIdOfThreadRecipient($db, $threadId)
        {
	         // get the user information
	        $userId = Zend_Auth::getInstance()->getIdentity()->user_id;

            // instantiate a Zend select object
            $select = $db->select();

        	// initialize the options
            $select->from(array('m' => 'mail'), 
            				array(  'm.user_id_to',
									'm.user_id_from',
								)
							)
            	   ->join(array('mb' => 'mail_body'),
            			  'mb.mail_id = m.mail_id',
            			   		array('')
            			 )
            	->where('m.thread_id in (?)', $threadId);            
            // set the offset, limit, and ordering of results
			$select->limit(1, 0);

            //Zend_Debug::dump($select->__toString());die;
            $results = $db->fetchAll($select);
            
            foreach ($results as $value) {
				//Zend_Debug::dump($value);die;
				if ($value['user_id_to'] == $value['user_id_from']) {
					$threadRecipient = $value['user_id_to'];
				} elseif ($value['user_id_to'] != $userId) {
					$threadRecipient = $value['user_id_to'];
				} elseif ($value['user_id_from'] != $userId) {
					$threadRecipient = $value['user_id_from'];
				}
            }
            
            return $threadRecipient;
        }
        
        /**
         * Updates the Mail table setting the current users
         * status for an entire thread to Deleted. Hacky hack
         * hack.
         *
         * @param database $db
         * @param int $threadId
         * @return true if successful
         */
        public static function updateDeletedMail($db, $threadId)
        {
	        $error = false;
	        $n = 0;
        	
        	// get the user information
	        $userId = Zend_Auth::getInstance()->getIdentity()->user_id;

            // instantiate a Zend select object
            $select = $db->select();


	        // loop through the threadId array
        	foreach ($threadId as $value) {

        		// set the default mail options
		        $threadOptions = array(
		            'thread_id'		=> $value
		        );

	            // run the base query
	            $select = self::_getBaseQuery($db, $threadOptions);
	            
	            // set the fields to select
	            $select->from(null, 
	            				array(  'm.mail_id'
									)
								)
	            	   ->join(array('mb' => 'mail_body'),
	            			  'mb.mail_id = m.mail_id',
	            			   		array(''))
	            	   ->where('m.user_id_to in (?) OR m.user_id_from in (?)', $userId
	            			 );
	            
	            // Get the mail ids we need to update to deleted status...
	            //Zend_Debug::dump($select->__toString());die;
	            $mail_ids = $db->fetchCol($select);
	            
	            foreach ($mail_ids as $v) {
		            // Update 2
		            $updates = array('user_from_deleted' => 1);

		            $where = array("user_id_from = $userId",
		            			   "mail_id	= $v");

		            $n += $db->update('mail', $updates, $where );
		            
		            // Update 2
		            $updates = array('user_to_deleted' => 1);

		            $where = array("user_id_to = $userId",
		            			   "mail_id = $v");

		            $n += $db->update('mail', $updates, $where);
		            
        			// Delete any mail status entries
        			//Zend_Debug::dump(sprintf('%s = %d, %s = %d', 'thread_id', $value, 'user_id', $userId));die;
		            $db->delete('mail_status',array('thread_id' => $value, 'user_id' => $userId));
	            }
        	}
			//Zend_Debug::dump($n);die;
			// $n represents the number of rows affected by the update
			if($n > 0) {
				return true;
			} else {
				return false;
			}
        }
        
         /**
		 * Static Private method: to form the basis
		 * of a select. For less code duplication
		 *
		 * @param database object $db
		 * @param array $options
		 * @return select object
		 */
        private static function _getBaseQuery($db, $options)
        {
            // initialize the options
            $defaults = array(
                'mail_id' => array()
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('m' => 'mail'), array());


            // filter results on specified mail ids (if any)
            if (!empty($options['mail_id'])) {
                $select->where('m.mail_id in (?)', $options['mail_id']);
            }

            // filter results on specified user ids (if any)
            if (!empty($options['user_id_from'])) {
                $select->where('m.user_id_from in (?)', $options['user_id_from']);
            }

            // filter results on specified user ids (if any)
            if (!empty($options['user_id_to'])) {
                $select->where('m.user_id_to in (?)', $options['user_id_to']);
            }

            // filter results on specified resource ids (if any)
            if (!empty($options['mail_subject'])) {
                $select->where('m.mail_subject in (?)', $options['mail_subject']);
            }
            
            // filter results on specified thread id (if any)
            if (!empty($options['thread_id'])) {
                $select->where('m.thread_id in (?)', $options['thread_id']);
            }
            
            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
        
        /**
         * Attempts to load a mail by a given userId
         *
         * @param int $user_id
         * @param int $mail_id
         * @return resource object
         */
        public function loadForUser($user_id, $thread_id)
        {
            $user_id = (int) $user_id;
            $thread_id = (int) $thread_id;

            if ($thread_id <= 0 || $user_id <= 0)
                return false;

            $query = sprintf(
                'select %s from %s where user_id_to = %d and thread_id = %d OR user_id_from = %d AND thread_id = %d;',
                join(', ', $this->getSelectFields()),
                $this->_table,
                $user_id,
                $thread_id,
                $user_id,
                $thread_id
            );

            //Zend_Debug::dump($query);die;
            return $this->_load($query);
        }

        /**
         * Updates a mail to a LIVE status
         *
         */
        public function sendLive()
        {
        	if ($this->is_active != self::STATUS_LIVE ) {
        		$this->is_active = self::STATUS_LIVE;
        		$this->date_edited = time();
        	}
        }
        
        /**
         * Checks the current LIVE / DRAFT status of a mail
         *
         * @return bool
         */
        public function isLive()
        {
        	return $this->isSaved() && $this->is_active == self::STATUS_LIVE;
        }
        
		/**
		 * Updates a mail to a DRAFT status
		 *
		 */
        public function sendBackToDraft()
		{
			$this->is_active = self::STATUS_DRAFT;
		}

		/**
		 * Updates a message to a READ status
		 *
		 */
        public function setAsRead()
		{
			$this->mail_status = self::MAIL_READ;
		}
    }