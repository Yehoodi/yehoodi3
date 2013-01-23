<?php
    class DatabaseObject_SiteDisallow extends DatabaseObject
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
            parent::__construct($db, 'site_disallow', 'disallow_id');

        	// These are required
            $this->add('user_name');
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
         * Returns disallowed user names
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getDisallowNames($db, $options = array())
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('sd.disallow_id', 'sd.user_name'
								)
							);

            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            
            $result = $db->fetchAll($select);
            
            return $result;
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
            $select->from(array('sd' => 'site_disallow'), array());

            if (count($options['user_name']) > 0)
                $select->where('sd.user_name = ?', $options['user_name']);

            if (count($options['disallow_id']) > 0)
                $select->where('sd.disallow_id in (?)', $options['disallow_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }