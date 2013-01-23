<?php
    class DatabaseObject_UserResourceNotify extends DatabaseObject
    {
    	const NOTIFIED_YES = 1;
    	const NOTIFIED_NO = 0;
    	
    	/**
    	 * Constructor:
    	 * 
    	 * Defines fields in the db
    	 * 
    	 * @param object $db
    	 */
        public function __construct($db)
        {
            parent::__construct($db, 'user_resource_notify', 'notify_id');

         	// Get current user_id if any
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {
        		$this->authUserId = $auth->getIdentity()->user_id;
        	}
        	
        	// These are required
            $this->add('user_id');
            $this->add('rsrc_id');
            $this->add('notify_status');
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
        
        /**
         * Returns All user who need to be
         * notified
         * 
         *
         * @param db object $db
         * @param array $options
         * @return user id(s) or false
         */
        public static function getNotifyUsers($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0
        	);

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('user_id'
								)
							)
            	   ->where('urn.rsrc_id in (?) AND urn.notify_status = ' . DatabaseObject_UserResourceNotify::NOTIFIED_NO , $options['rsrc_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchAll($select)) {
            	return $result;
            }
            
            return false;
        }
                
        /**
         * Returns the notify id
         * for a user and resource
         * 
         *
         * @param db object $db
         * @param array $options
         * @return notify_id or false
         */
        public static function getNotify($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0
        	);

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('notify_id'
								)
							)
            	   ->where('urn.rsrc_id = ?', $options['rsrc_id']
            	   			);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchOne($select)) {
            	return $result;
            }
            
            return false;
        }
                
        /**
         * Returns the rsrc_ids
         * for a user
         * 
         *
         * @param db object $db
         * @param array $options
         * @return array
         */
        public static function getAllNotifyByUserId($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0
        	);

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('urn.rsrc_id'
								)
							)
					->join(array('r' => 'resource'),
            				'urn.rsrc_id = r.rsrc_id',
            						array()
            			);

			$select->where('r.is_active = ?', 1);

			// set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);
            	
            return $result;
        }
                
        /**
         * Get the number of watches added
         * by a userId
         *
         * @param db object $db
         * @param int $id
         * @return int
         */
        public static function getUserNotifyCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getUserNotifyCount

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
            $select->from(array('urn' => 'user_resource_notify'), array());


            // filter results on specified status ids (if any)
            if (count($options['user_id']) > 0)
                $select->where('urn.user_id in (?)', $options['user_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }