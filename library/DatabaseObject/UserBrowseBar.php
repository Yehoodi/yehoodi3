<?php
    class DatabaseObject_UserBrowseBar extends DatabaseObject
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
            parent::__construct($db, 'user_browse_bar', 'user_id');

         	// Get current user_id if any
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {
        		$this->authUserId = $auth->getIdentity()->user_id;
        	}
        	
        	// These are required
            $this->add('rsrc_id');
            $this->add('cat_id');
            $this->add('sort_order');
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
         * Returns All votes for a user
         * 
         *
         * @param db object $db
         * @param array $options
         * @return vote id or false
         */
        public static function getAllVotesByUserId($db, $options)
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
            				array('rsrc_id'
								)
							)
            	   ->where('uv.user_id in (?)', $options['user_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchPairs($select)) {
            	return $result;
            }
            
            return false;
        }
        
        /**
         * Returns All votes for a resource id
         * 
         *
         * @param db object $db
         * @param array $options
         * @return int number of votes
         */
        public static function getVoteCountByResourceId($db, $options)
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
            				array('COUNT(*)'
								)
							)
            	   ->where('uv.rsrc_id = (?)', $options['rsrc_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            return $result;
            
        }
        
        /**
         * Returns vote id (by resource id) if found
         * otherwise returns false
         *
         * @param db object $db
         * @param array $options
         * @return vote id or false
         */
        public static function getVote($db, $options)
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
            // TODO: get the table names from the $tableConfig
            $select->from(null, 
            				array('vote_id'
								)
							)
            	   ->where('uv.rsrc_id = ?', $options['rsrc_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchOne($select)) {
            	return $result;
            }
            
            return false;
        }
        
        public static function addVote($rsrc_id)
        {
        	$this->rsrc_id = $rsrc_id;
        	$this->user_id = $this->authUserId;
        	$this->save();
        	
        	return;
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
            $select->from(array('uv' => 'user_vote'), array());


            // filter results on specified status ids (if any)
            if (count($options['user_id']) > 0)
                $select->where('uv.user_id in (?)', $options['user_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }