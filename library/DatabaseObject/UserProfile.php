<?php
    class DatabaseObject_UserProfile extends DatabaseObject
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
            parent::__construct($db, 'user_profile', 'user_id');

        	// These are required
            $this->add('user_id');
            $this->add('profile_key');
            $this->add('profile_value');
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
         * Returns user profile data if any
         * otherwise returns false
         *
         * @param db object $db
         * @param array $options
         * @return string of data or false
         */
        public static function getUserProfileData($db, $options)
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchOne($select)) {
            	return $result;
            }
            
            return false;
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
            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('up' => 'user_profile'), array('profile_value'));


            // filter results on specified status ids (if any)
            if (!empty($options['user_id'])) {
                $select->where('up.user_id = ?', $options['user_id']);
            }

            if (!empty($options['profile_key'])) {
                $select->where('up.profile_key = ?', $options['profile_key']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }