<?php
    class DatabaseObject_Category extends DatabaseObject
    {

        public function __construct($db)
        {
            parent::__construct($db, 'category', 'cat_id');

            // These are required
            $this->add('rsrc_type_id');
            $this->add('cat_type');
            $this->add('cat_site_url');
            $this->add('order');

            //$this->meta = new DatabaseObject_Meta();
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
        }

        protected function preDelete()
        {
        }

        /**
         * Retreive the list of categories for the given
         * resource id or returns ALL categories
         *
         * @param database object $db
         * @param array $options
         * @return object
         */
        public static function getCategories($db, $options)
        {
            // If we don't get a rsrc_type_id, then unset the
            // variable so we can get them all
        	if (!$options['rsrc_type_id']) {
            	unset($options['rsrc_type_id']);
            	$options['order'] = 'c.rsrc_type_id';
            }
        	
        	$select = self::_GetBaseQuery($db, $options);
            $select->from(null, 
            				array(  'c.cat_id',
									'c.rsrc_type_id',
									'c.cat_type',
									'c.cat_site_url',
									'rt.rsrc_type'
								)
						)
            	   ->join(array('rt' => 'resource_type'),
            			  'c.rsrc_type_id = rt.rsrc_type_id',
            			  array()

        				);

            // set the offset, limit, and ordering of results
            if (!empty($options['limit']))
            	$select->limit($options['limit'], $options['offset']);
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }
        
        /**
         * Gets all categories under a 
         * specific resource type Id
         *
         * @param unknown_type $db
         * @param unknown_type $options
         * @return unknown
         */
        public static function getCategoryByResourceTypeId($db, $options)
        {
            $select = self::_GetBaseQuery($db, $options);
            $select->from(null, 
            				array(  'cat_id',
									'cat_type'
								)
        				);

            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }//getCategoryByResourceTypeId()
        
        /**
         * Gets the default (first) catId for a 
         * specific resource type Id
         *
         * @param obj $db
         * @param array $options
         * @return int catId
         */
        public static function getDefaultCatIdByResourceTypeId($db, $options)
        {
            $select = self::_GetBaseQuery($db, $options);
            $select->from(null, 
            				array(  'cat_id'
								)
        				);

			$select->limit(1,0);

			if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }//getCategoryByResourceTypeId()
        
        /**
         * Gets resource type id from a 
         * specific category type Id
         *
         * @param database object $db
         * @param array $options
         * @return int rsrc_type_id
         */
        public static function getResourceTypeIdByCategoryId($db, $options)
        {
            $select = self::_GetBaseQuery($db, $options);
            $select->from(null, 
            				array('rsrc_type_id'
								)
        				);

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }//getResourceTypeIdByCategoryId()
        
        public static function getCatIdByUrl($db, $url)
        {
            $url = trim($url);
            
            if (strlen($url) == 0) {
                return false;
            }

			if ($url == 'all') {
                return false;
    		}
			
            // instantiate a Zend select object
            $select = $db->select();
            $select->from('category', 
            				array('cat_id',
								)
            			 );
	        $select->where('cat_site_url = (?)', $url);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }
        
        public static function getCategoryTextByUrl($db, $url)
        {
            $url = trim($url);
            
			if ($url == 'all')
				return 'all topics';
				
            if (strlen($url) == 0)
                return false;

            // instantiate a Zend select object
            $select = $db->select();
            $select->from('category', 
            				array('cat_type',
								)
            			 );
	        $select->where('cat_site_url = (?)', $url);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }
        
        public static function getCategoryTextById($db, $id = null)
        {
            if (!$id) {
            	return "";
            }
        	
        	// instantiate a Zend select object
            $select = $db->select();
            $select->from('category', 
            				array('cat_type',
								)
            			 );
	        $select->where('cat_id = (?)', $id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }
        
        /**
         * Gets all the categories reguardless
         * of resource id
         *
         * @param unknown_type $db
         * @param unknown_type $options
         * @return unknown
         */
        public static function getCategoriesAndResources($db, $options)
        {
        	$select = self::_GetBaseQuery($db, $options);
            $select->from(null, 
            				array(  'c.cat_id',
									'c.rsrc_type_id',
									'c.cat_type',
									'c.cat_site_url',
									'c.order',
									'rt.rsrc_type'
									)
            				)
            	   ->join(array('rt' => 'resource_type'),
            			  'c.rsrc_type_id = rt.rsrc_type_id',
            			  array()
            			 );

            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }

           	//Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
        }
        
        public static function getCategoryCount($db, $options)
        {
            $select = self::_GetBaseQuery($db, $options);
            $select->from(null, 'count(*)');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        private static function _GetBaseQuery($db, $options)
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

            // create a query that selects from the users table
            $select->from(array('c' => 'category'), array());


            // filter results on specified user ids (if any)
            if (!empty($options['cat_id'])) {
                $select->where('c.cat_id in (?)', $options['cat_id']);
            }

            // filter results on specified resource type id (if any)
            if (!empty($options['rsrc_type_id'])) {
                $select->where('c.rsrc_type_id in (?)', $options['rsrc_type_id']);
            }
            
            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }
?>