<?php
    class DatabaseObject_Comment extends DatabaseObject
    {

        public $meta = null;
        public $userMeta = null;
        public $neatCommentDate = null;
        public $replyToLink;
        public $replyToPostNum;
        public $replyToUser;
        public $ignored = false;
        
        public $commentRaw = null; // Raw version of the comment from the db

        protected $dateTime;
        protected $filterDirty = '';
        
        const STATUS_DRAFT = 0;
        const STATUS_LIVE = 1;

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
            parent::__construct($db, 'comment', 'comment_id');

        	// Set up new date time
            $this->dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));

            // These are required
            $this->add('rsrc_id');
            $this->add('user_id');
            $this->add('comment_num');
            $this->add('comment');
            $this->add('reply_to_id',0);
            $this->add('date_created', $this->dateTime->format("Y-m-d H:i:s"));
            $this->add('edit_user_id');
            $this->add('date_edited',"0000-00-00 00:00:00");
            $this->add('date_last_active',"0000-00-00 00:00:00");
            $this->add('date_deactive',"0000-00-00 00:00:00");
            $this->add('deactive_user_id',0);
            $this->add('remote_ip',ip2long('127.0.0.1'));
            $this->add('is_active',self::STATUS_DRAFT);
        }

        protected function preInsert()
        {
        	return true;
        }

        protected function postLoad()
        {
        	//
        	// User stuff
        	//
        	// Get current user_id if any
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {
        		$user_id = $auth->getIdentity()->user_id;
        		// User has dirty words filtered?
        		$this->filterDirty = DatabaseObject_User::checkDirtyFilter($this->_db, $user_id);
        		
        		// User is being ignored?
        		$this->ignored = DatabaseObject_UserIgnore::getUserIgnoreStatus($this->_db,array('user_id' => $user_id,
        																					'ignored_user_id' => $this->user_id ));
				// Update user_resource_notify table
				if ($notifyId = DatabaseObject_UserResourceNotify::getNotify($this->_db,array('user_id' => $user_id,
																									'rsrc_id' => $this->rsrc_id ))) {
					// user's status is found and should be reset to NOTIFIED_NO
					$notify = new DatabaseObject_UserResourceNotify($this->_db);
					$notify->load($notifyId);
					$notify->notify_status = DatabaseObject_UserResourceNotify::NOTIFIED_NO ;
					$notify->save();
				}
        	}

        	// instantiate the comment meta object
        	$this->meta = new DatabaseObject_CommentMeta($this->_db, $this);
        	
        	// set the formatting of the date and add to the meta
        	$this->meta->neatPostedDate = common::neatDate($this->date_created);
        	
            // Get the user meta info
            $this->userMeta = new DatabaseObject_UserMeta($this->_db, $this->user_id);
            
        	// Added nl2br to show proper line spacing in comments
        	$this->comment = html_entity_decode($this->comment);
        	
        	// Dirty words filtered by user or default?
        	//Zend_Debug::dump($this->filterDirty);die;
			if ($this->filterDirty != 'off') {
				$this->comment = UtilityController::cleanDirtyWords($this->comment);
			}
			
			// Generate the reply to link if any
            if ($this->reply_to_id > 0) {
            	// Get the reply to username
            	$this->replyToUser = $this->getUserNameByCommentId($this->_db, array('comment_id' => $this->reply_to_id));
            	$this->replyToPostNum = $this->getCommentNumberByCommentId($this->_db, array('comment_id' => $this->reply_to_id));
            	$this->replyToLink = $this->reply_to_id;
            }

        }

        protected function postInsert()
        {
        	return true;
        }

        protected function postUpdate()
        {

        }

        protected function preDelete()
        {
        	return true;
        }

        /**
         * Returns the current comment_num
         *
         * @return unknown
         */
        public function getCommentNum()
        {
            return (int) $this->comment_num;
        }

        /**
         * Returns the date the comment was edited
         *
         * @return date string
         */
        public function getEditDate()
        {
            return common::neatDateTime($this->date_edited) . ' (' . common::getRelativeTime($this->date_edited) . ')';
        }

        /**
         * Gets all comments based
         * on the $options array
         *
         * @param object $db
         * @param array $options
         * @return array of comment objects
         */
        public static function getComments($db, $options)
        {
        	// initialize the options
        	// Note: the limit in here messes up the search RebuildIndex method
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> ''
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'c.comment_id',
									'c.rsrc_id',
									'c.user_id',
									'c.comment_num',
									'c.comment',
									'c.reply_to_id',
									'c.date_created',
									'c.edit_user_id',
									'c.date_edited',
									'c.date_last_active',
									'c.is_active',
									'c.date_deactive',
									'c.deactive_user_id',
									'c.remote_ip'
								)
							)
            	   ->join(array('u' => 'user'),
            			  'c.user_id = u.user_id',
            			   		array('user_name')
            			 );
            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die();
            $data = $db->fetchAll($select);

            // turn data into array of DatabaseObject_Comment objects
            $comments = self::BuildMultiple($db, __CLASS__, $data);
            $comment_ids = array_keys($comments);
            
            if(count($comment_ids) == 0)
            	return array();
            	
            return $comments;
        } //getComments()
        
        /**
         * Gets one comments based
         * on the $options array
         *
         * @param object $db
         * @param array $options
         * @return array of comment objects
         */
        public static function getCommentById($db, $options)
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
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'c.comment_id',
									'c.rsrc_id',
									'c.user_id',
									'c.comment_num',
									'c.comment',
									'c.reply_to_id',
									'c.date_created',
									'c.edit_user_id',
									'c.date_edited',
									'c.date_last_active',
									'c.is_active',
									'c.date_deactive',
									'c.deactive_user_id',
									'c.remote_ip'
								)
							)
            	   ->join(array('u' => 'user'),
            			  'c.user_id = u.user_id',
            			   		array('user_name')
            			 );
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $comment = $db->fetchRow($select);

            return $comment;
        } //getCommentById()
        
        /**
         * Gets the usernames that
         * posted under this IP address
         *
         * @param object $db
         * @param array $options
         * @return array of comment objects
         */
        public static function getUserNameForIP($db, $options)
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'u.user_name'
								)
							)
            	   ->join(array('u' => 'user'),
            			  'c.user_id = u.user_id',
            			   		array()
            			 )
					->group(array('c.user_id'))
                    ->where('c.remote_ip = ?', $options['remote_ip']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $comment = $db->fetchAll($select);

            return $comment;
        } 
        
        /**
         * Gets the IP addresses
         * posted under this user_id
         *
         * @param object $db
         * @param array $options
         * @return array of comment objects
         */
        public static function getIPForUserName($db, $options)
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'c.remote_ip'
								)
							)
                    ->where('c.remote_ip > ?', 0)
					->group(array('c.remote_ip'));
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $comment = $db->fetchAll($select);

            return $comment;
        } 
        
        /**
         * Get the count for pagination, etc...
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getCommentCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);

            $select->from(null, 'count(*)'
            			 );
            //Zend_Debug::dump($select->__toString());die;
            return (int) $db->fetchOne($select);
        } // getCommentCount

        /**
         * Get the user_id and last comment_num count 
         * for a specific rsrc_id.
         *
         * @param db object $db
         * @param array $options
         * @return array
         */
        public static function getTrackingInfo($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);

            $select->from(null, array('comment_num',
                                      'user_id')
            			 );
            			 
            $select->limit(1);
            $select->order('comment_num DESC');
            
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchRow($select);
            return $result;
        } // getTrackingInfo

        /**
         * Get the # of comments from this
         * IP address
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getPostIPCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)')
            ->where('remote_ip = ?', $options['remote_ip']);
            //Zend_Debug::dump($select->__toString());die;
            return (int) $db->fetchOne($select);
        } // getPostIPCount

        /**
         * Get the comments author (user name)
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getUserNameByCommentId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array()
							)
            	   ->join(array('u' => 'user'),
            			  'c.user_id = u.user_id',
            			   		array('user_name')
            			 );

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getUserNameByCommentId

        /**
         * Get the comment number for this comment
         * 
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getCommentNumberByCommentId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('c.comment_num')
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getCommentNumberByCommentId

        /**
         * Get the comments text only
         *
         * @param db object $db
         * @param array $options
         * @return array text
         */
        public static function getCommentTextByCommentId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('c.comment')
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchRow($select);
        } // getCommentTextByCommentId

        /**
         * Get the next comment count number
         * for insert into db
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getNextCommentCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)'
            			);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select) + 1;
        } // getNextCommentCount


        /**
         * Get the number of comments submitted
         * by a userId
         *
         * @param db object $db
         * @param int $id
         * @return int
         */
        public static function getUserCommentCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)');
            
            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getUserCommentCount

        /**
         * Get the number of comments submitted
         * by a userId
         *
         * @param db object $db
         * @param int $id
         * @return int
         */
        public static function getLastCommentIdByResourceId($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> 'c.date_created DESC'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_getBaseQuery($db, $options);
            $select->from(null, array('c.date_created'));

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);

            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);

            return $result;
        } // getLastCommentIdByResourceId

        /**
         * Gets latest comments
         * on the $options array
         *
         * @param object $db
         * @param array $options
         * @return array of resource ids
         */
        public static function getLatestComments($db, $options)
        {
        	// initialize the options
        	// Note: the limit in here messes up the search RebuildIndex method
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 400,
        		'order'		=> 'date_created DESC'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'c.rsrc_id',
									'MAX(c.date_created) AS date_created'
								)
							)
					->group(array('c.rsrc_id')
            			 );

            // filter results on specified r.rsrc_date (if any)
            if (!empty($options['rsrc_date'])) {
                $select->where('c.date_created >= ?', $options['rsrc_date']);
            }

            if(!empty($options['cat_id'])) {
            	$select->joinLeft(array('r' => 'resource'),
            			  "r.cat_id = {$options['cat_id']} AND c.rsrc_id = r.rsrc_id",
            			   		array());
				$select->where('r.cat_id IS NOT NULL');
            }
            
            
			// set the offset, limit, and ordering of results
            if (!empty($options['limit']))
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);

            return $data;
        } //getComments()

        /**
         * Get the last comment date that was posted
         * 
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getLastCommentDate($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('last_comment'=> 'MAX(c.date_created)')
							);

            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            if (!$result) {
            	return '0000-00-00 00:00:00';
            } else {
	            return $result;
            }
        } // getLastCommentDate


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
                'cat_id' => array(),
                'from'    => '',
                'to'      => ''
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('c' => 'comment'), array());


            // filter results on specified comment ids (if any)
            if (isset($options['comment_id']))
                $select->where('c.comment_id in (?)', $options['comment_id']);

            // filter results on specified user ids (if any)
            if (isset($options['user_id']))
                $select->where('c.user_id in (?)', $options['user_id']);

            // filter results on specified resource ids (if any)
            if (isset($options['rsrc_id']))
                $select->where('c.rsrc_id in (?)', $options['rsrc_id']);

            // filter results on specified r.rsrc_date (if any)
            if (isset($options['date_created']))
                $select->where('c.date_created >= ?', $options['date_created']);
            
            // filter results on ACTIVE or DRAFT (if any)
            if (isset($options['is_active']))
                $select->where('c.is_active = ?', $options['is_active']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
        
        /**
         * Attempts to load a comment by a given userId
         * or if the user is a moderator
         *
         * @param object $identity
         * @param int $comment_id
         * @return resource object
         */
        public function loadForUser($identity, $comment_id)
        {
            $comment_id = (int) $comment_id;
            $user_id = (int) $identity->user_id;

            if ($comment_id <= 0 || $user_id <= 0)
                return false;

            if($identity->mod) {
	            $query = sprintf(
	                'select %s from %s where comment_id = %d',
	                join(', ', $this->getSelectFields()),
	                $this->_table,
	                $comment_id
	            );
            } else {
	            $query = sprintf(
	                'select %s from %s where user_id = %d and comment_id = %d',
	                join(', ', $this->getSelectFields()),
	                $this->_table,
	                $user_id,
	                $comment_id
	            );
            }

            return $this->_load($query);
        }

        /**
         * Updates a comment to a LIVE status
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
         * Checks the current LIVE / DRAFT status of a comment
         *
         * @return bool
         */
        public function isLive()
        {
        	return $this->isSaved() && $this->is_active == self::STATUS_LIVE;
        }
        
		/**
		 * Updates a comment to a DRAFT status
		 *
		 */
        public function sendBackToDraft()
		{
			$this->is_active = self::STATUS_DRAFT;
		}

    }