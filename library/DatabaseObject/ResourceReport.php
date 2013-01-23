<?php
    class DatabaseObject_ResourceReport extends DatabaseObject
    {
    	const NOTIFIED_YES = 1;
    	const NOTIFIED_NO = 0;

    	public function __construct($db)
        {
            parent::__construct($db, 'resource_report', 'report_id');

            // These are required
            $this->add('rsrc_id');
            $this->add('user_id');
            $this->add('report_status');
        }
                
        protected function preInsert()
        {
        	return TRUE;
        }//preInsert

        /**
         * Do this stuff after the object is loaded
         *
         */
        protected function postLoad()
        {
        	
        }//postLoad

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
         * Gets all resource_types 
         *
         * @param unknown_type $db
         * @param unknown_type $options
         * @return unknown
         */
        public static function getAllReports($db, $options)
        {
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> 'order'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'rr.report_id'
								)
        				);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }//getCategoryByResourceTypeId()
        
        /**
         * Returns the report id
         * for a user and resource
         * 
         *
         * @param db object $db
         * @param array $options
         * @return report_id or false
         */
        public static function getReport($db, $options)
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
            				array('report_id'
								)
							);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            if ($result = $db->fetchOne($select)) {
            	return $result;
            }
            
            return false;
        }
        
        private static function _getBaseQuery($db, $options)
        {
            // initialize the options
            $defaults = array(
                'rsrc_id' => array(),
                'from'    => '',
                'to'      => ''
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // instantiate a Zend select object
            $select = $db->select();

            // create a query that selects from the users table
            $select->from(array('rr' => 'resource_report'), array());


            // filter results on specified resource ids (if any)
            if (count($options['rsrc_id']) > 0) {
                $select->where('rr.rsrc_id = ?', $options['rsrc_id']);
            }
            
            // filter results on specified user ids (if any)
            if (count($options['user_id']) > 0) {
                $select->where('rr.user_id = ?', $options['user_id']);
            }
            
            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }