<?php
    class DatabaseObject_UserCalendar extends DatabaseObject
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
            parent::__construct($db, 'user_calendar', 'calendar_id');

         	// Get current user_id if any
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {
        		$this->authUserId = $auth->getIdentity()->user_id;
        	}
        	
        	// These are required
            $this->add('user_id');
            $this->add('rsrc_id');
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
         * Returns All calendars for a user
         * 
         *
         * @param db object $db
         * @param array $options
         * @return calendar id or false
         */
        public static function getAllCalendarsByUserId($db, $options)
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
            	   ->where('uc.user_id in (?)', $options['user_id']
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
         * Returns calendar id (by resource id) if found
         * otherwise returns false
         *
         * @param db object $db
         * @param array $options
         * @return calendar id or false
         */
        public static function getCalendar($db, $options)
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
            				array('calendar_id'
								)
							)
            	   ->where('uc.rsrc_id = (?)', $options['rsrc_id']
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());
            if ($result = $db->fetchOne($select)) {
            	return $result;
            }
            
            return false;
        }
        
        public static function addCalendar($rsrc_id)
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
            $select->from(array('uc' => 'user_calendar'), array());


            // filter results on specified status ids (if any)
            if (count($options['user_id']) > 0)
                $select->where('uc.user_id in (?)', $options['user_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }