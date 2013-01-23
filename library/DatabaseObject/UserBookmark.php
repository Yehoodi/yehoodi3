<?php
    class DatabaseObject_UserBookmark extends DatabaseObject
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
            parent::__construct($db, 'user_bookmark', 'bookmark_id');

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
         * Returns All bookmark ids for a user
         * 
         *
         * @param db object $db
         * @param array $options
         * @return bookmark id or false
         */
        public static function getAllBookmarksByUserId($db, $options)
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('ub.rsrc_id'
								)
            	   			)
					->join(array('r' => 'resource'),
            				'ub.rsrc_id = r.rsrc_id',
            						array()
            			);
            			
			$select->where('r.is_active = ?', 1);

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0) {
            	$select->limit($options['limit'], $options['offset']);
            }
            	
            if (!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($options);die;
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);

			return $result;
            
        }
        
        /**
         * Returns bookmark id (by resource id) if found
         * otherwise returns false
         *
         * @param db object $db
         * @param array $options
         * @return bookmark id or false
         */
        public static function getBookmark($db, $options)
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
            				array('bookmark_id'
								)
							)
            	   ->where('ub.rsrc_id = (?)', $options['rsrc_id']
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
            	return $result;
            }
            
            return false;
        }
        
        /**
         * Get the number of bookmarks added
         * by a userId
         *
         * @param db object $db
         * @param int $id
         * @return int
         */
        public static function getUserBookmarkCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getUserBookmarkCount

        /**
         * Adds a bookmark for the user
         *
         * @param unknown_type $rsrc_id
         */
        public static function addBookmark($rsrc_id)
        {
        	$this->rsrc_id = $rsrc_id;
        	$this->user_id = $this->authUserId;
        	$this->save();
        	
        	return;
        }
        
        /**
         * Clears the user's bookmark memcached variable
         * (Used mostly with the ajax bookmark buttons)
         *
         * @param int $userId
         */
        public static function updateBookmarkCache($userId)
        {
            $memcache = new Memcache;
            $memcache->connect("localhost",11211);

            $masterKey = DatabaseObject_Resource::getResourceCacheId();
            
            $memcache->set("{$masterKey}:{$userId}:bookmarks", '', false, 1);
            $memcache->set("{$masterKey}:{$userId}:bookmarksCount", '', false, 1);
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
            $select->from(array('ub' => 'user_bookmark'), array());


            // filter results on specified status ids (if any)
            if (count($options['user_id']) > 0)
                $select->where('ub.user_id in (?)', $options['user_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }