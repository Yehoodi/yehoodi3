<?php
    class DatabaseObject_UserIgnore extends DatabaseObject
    {
    	/**
    	 * Constructor:
    	 * 
    	 * Defines fields in the db
    	 * 
    	 * @param object $db
    	 */
        public function __construct($db)
        {
            parent::__construct($db, 'user_ignore', 'ignore_id');

         	// Get current user_id if any
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {
        		$this->authUserId = $auth->getIdentity()->user_id;
        	}
        	
        	// These are required
            $this->add('user_id');
            $this->add('ignored_user_id');
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
         * Returns All ignored user ids for a user
         * 
         *
         * @param db object $db
         * @param array $options
         * @return user id(s) or false
         */
        public static function getIgnoredUsersByUserId($db, $options)
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
            				array('ignored_user_id'
								)
							)
            	   ->where('ui.user_id in (?)', $options['user_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0) {
            	$select->limit($options['limit'], $options['offset']);
            }
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);

            return $result;
            
        }
        
        /**
         * Returns ignore id (by user id) if found
         * otherwise returns false
         *
         * @param db object $db
         * @param array $options
         * @return true or false
         */
        public static function getUserIgnoreStatus($db, $options)
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
            				array('ignore_id'
								)
							)
            	   ->where('ui.ignored_user_id = (?)', $options['ignored_user_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0) {
            	$select->limit($options['limit'], $options['offset']);
            }
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());
            if ($result = $db->fetchOne($select)) {
            	//return $result;
            	return true;
            }
            
            return false;
        }
        
        /**
         * Returns ignore id (by ignored_user_id) if found
         * otherwise returns false
         *
         * @param db object $db
         * @param array $options
         * @return ignore_id or false
         */
        public static function getIgnore($db, $options)
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
            				array('ignore_id'
								)
							)
            	   ->where('ui.ignored_user_id = (?)', $options['ignored_user_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0) {
            	$select->limit($options['limit'], $options['offset']);
            }
            
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchOne($select)) {
            	return $result;
            }
            
            return false;
        }
        
        /**
         * Get the total number of ignored
         * users for a user_id
         *
         * @param db object $db
         * @param int $id
         * @return int
         */
        public static function getUserIgnoreCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getUserVoteCount

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
            $select->from(array('ui' => 'user_ignore'), array());


            // filter results on specified status ids (if any)
            if (count($options['user_id']) > 0)
                $select->where('ui.user_id in (?)', $options['user_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }