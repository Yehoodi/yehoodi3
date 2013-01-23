<?php
    class DatabaseObject_ResourceType extends DatabaseObject
    {

        // Static vars for resource types
        public static $featured =		1;
        public static $lindy =			2;
        public static $event =			3;
        public static $lounge =			4;
        public static $biz =			5;
        public static $admin =			99;

        public function __construct($db)
        {
            parent::__construct($db, 'resource_type', 'rsrc_type_id');

            // These are required
            $this->add('rsrc_type');
            $this->add('rsrc_name');
            $this->add('order');
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
        public static function getAllResourceTypes($db)
        {
        	$options = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> 'order'
        	);
        	
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'rt.rsrc_type',
            						'rt.rsrc_type_id'
								)
        				);

        	// set the offset, limit, and ordering of results
            if ($options['limit'] > 0) {
            	$select->limit($options['limit'], $options['offset']);
            }
            
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchPairs($select);
        }//getCategoryByResourceTypeId()
        
        /**
         * Gets a rsrc_type_id
         * from a url string 
         *
         * @param database object $db
         * @param string $url
         * @return int rsrc_type_id or false
         */
        public static function getResourceTypeIdByUrl($db, $url)
        {
            // instantiate a Zend select object
            $select = $db->select();

            // create a query that selects from the resource_type table
            $select->from(array('rt' => 'resource_type'), array());

            $select->from(null, 
            				array(  'rt.rsrc_type_id'
								)
        				);

            // filter results on specified url
			$select->where('rt.rsrc_type = ?', $url);

            
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }//getCategoryByResourceTypeId()
        
        private static function _getBaseQuery($db, $options)
        {
            // initialize the options
            $defaults = array(
                'rsrc_type_id' => array(),
                'from'    => '',
                'to'      => ''
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // instantiate a Zend select object
            $select = $db->select();

            // create a query that selects from the users table
            $select->from(array('rt' => 'resource_type'), array());


            // filter results on specified user ids (if any)
            if (count($options['rsrc_type_id']) > 0) {
                $select->where('rt.rsrc_type_id in (?)', $options['rsrc_type_id']);
            }
            
            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }