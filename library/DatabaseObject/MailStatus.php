<?php
    class DatabaseObject_MailStatus extends DatabaseObject
    {

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
            parent::__construct($db, 'mail_status', 'id');

            // These are required
            $this->add('id');
            $this->add('thread_id');
            $this->add('user_id');
            $this->add('mail_status');
        }

        protected function preInsert()
        {
        	return true;
        }

        protected function postLoad()
        {

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
        
        public static function getMailStatus($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 1
        	);

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('mail_status'
								)
							)
            	   ->where('ms.thread_id = (?)', $options['thread_id']
            	   			)
            	   ->where('ms.user_id = (?)', $options['user_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            return $result;
        }
        
        /**
         * Get the new mail count
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getNewMailCount($db, $options)
        {
        	$select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				'count(*)'
							)
            		->where('ms.user_id = ?', $options['user_id'])
            		->where('ms.mail_status = ?', DatabaseObject_Mail::MAIL_NEW );

            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            return $result;
        } // getNewMailCount

        /**
         * Get the id of the mail status
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getMailStatusId($db, $options)
        {
        	$select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				'id'
							)
            		->where('ms.thread_id = ?', $options['thread_id'] )
            		->where('ms.user_id = ?', $options['user_id'] );

            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            return $result;
        } // getNewMailCount

        /**
         * Updates the Mail table setting the current users
         * mail thread to read status. 
         *
         * @param database $db
         * @param int $threadId
         */
        public static function markAsRead($db, $threadId)
        {
        	//Zend_Debug::dump($threadId);die;
        	// get the user information
	        $userId = Zend_Auth::getInstance()->getIdentity()->user_id;

            // instantiate a Zend select object
            $select = $db->select();

	        // loop through the threadId array
        	foreach ($threadId as $value) {

	            // instantiate a Zend select object
	            $select = $db->select();
	
	            // define the table to pull from
	            $select->from(array('ms' => 'mail_status'), array());

	            // set the fields to select
	            $select->from(null, 
	            				array(  'ms.id'
									)
								)
	            	   ->where('ms.thread_id = ?', $value)
	            	   ->where('ms.user_id = ?', $userId);
	            
	            // Get the mail ids we need to update to deleted status...
	            //Zend_Debug::dump($select->__toString());die;
	            $mail_ids = $db->fetchCol($select);
	            
	            //Zend_Debug::dump($mail_ids);die;
	            
	            foreach ($mail_ids as $v) {
		            // Load them and delete them
					$mailId = new DatabaseObject_MailStatus($db);
					$mailId->load($v);
					$mailId->delete();
	            }
        	}
        }            

        /**
         * Updates the Mail table setting the current users
         * mail thread to new status. 
         *
         * @param database $db
         * @param int $threadId
         */
        public static function markAsNew($db, $threadId)
        {
        	//Zend_Debug::dump($threadId);die;
        	// get the user information
	        $userId = Zend_Auth::getInstance()->getIdentity()->user_id;

            // instantiate a Zend select object
            $select = $db->select();

	        // loop through the threadId array
        	foreach ($threadId as $value) {

	            // instantiate a Zend select object
	            $select = $db->select();
	
	            // define the table to pull from
	            $select->from(array('ms' => 'mail_status'), array());

	            // set the fields to select
	            $select->from(null, 
	            				array(  'ms.id'
									)
								)
	            	   ->where('ms.thread_id = ?', $value)
	            	   ->where('ms.mail_status = ?', DatabaseObject_Mail::MAIL_NEW )
	            	   ->where('ms.user_id = ?', $userId);
	            
	            // Get the mail ids we need to update to deleted status...
	            //Zend_Debug::dump($select->__toString());die;
	            if(!$mail_status_id = $db->fetchOne($select)) {
		            // Add thread status to table
					$mailId = new DatabaseObject_MailStatus($db);
					$mailId->thread_id = $value;
					$mailId->user_id = $userId;
					$mailId->mail_status = DatabaseObject_Mail::MAIL_NEW ;
					$mailId->save();
	            }
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
                'id' => array()
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('ms' => 'mail_status'), array());


            // filter results on specified status ids (if any)
            if (count($options['id']) > 0)
                $select->where('ms.id in (?)', $options['id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }