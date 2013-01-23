<?php
    class DatabaseObject_SiteBan extends DatabaseObject
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
            parent::__construct($db, 'site_ban', 'ban_id');

        	// These are required
            $this->add('user_id',0);
            $this->add('email_address','');
            $this->add('remote_ip','');
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
         * Returns the IP address in standard
         * IPv4 xxx.xxx.xxx format for the screen
         *
         * @return ipv4 format string
         */
        public function getIPAddress()
        {
        	return long2ip($this->remote_ip);
        }
        
        /**
         * Returns all banned user names
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getBannedUsers($db, $options = array())
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('sb.ban_id', 'sb.user_id'
								)
							)
            	   ->join(array('u' => 'user'),
            			  'sb.user_id = u.user_id',
            			   		array('user_name')
            			 );

            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            
            $result = $db->fetchAll($select);
            
            return $result;
        }

        /**
         * Returns all banned IP addresses
         * and ban IDs
         *
         * @param db object $db
         * @param array $options
         * @return array of IP addresses
         */
        public static function getBannedIPs($db, $options = array())
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('sb.ban_id','sb.remote_ip'
								)
            			 )
            		->where('sb.remote_ip != 0');

            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            
            $result = $db->fetchAll($select);
            
            foreach ($result as &$value) {
            	$value['remote_ip'] = long2ip($value['remote_ip']);
            }
            
            //Zend_Debug::dump($result);die;
            return $result;
        }

        /**
         * Returns all banned IP addresses
         *
         * @param db object $db
         * @param array $options
         * @return array of IP addresses
         */
        public static function getBannedIPArray($db, $options = array())
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('sb.remote_ip'
								)
            			 )
            		->where('sb.remote_ip != 0');

            if(!empty($options['order'])) {
            	$select->order($options['order']);
            }
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            
            $result = $db->fetchCol($select);
            
            foreach ($result as &$value) {
            	$value = long2ip($value);
            }
            
            //Zend_Debug::dump($result);die;
            return $result;
        }

        /**
         * Returns all banned email addresses
         *
         * @param db object $db
         * @param array $options
         * @return array of email addresses
         */
        public static function getBannedEmails($db, $options = array())
        {
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('sb.ban_id','sb.email_address'
								)
            			 )
            		->where('sb.email_address != ? ','');

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
            $select->from(array('sb' => 'site_ban'), array());

            // filter results on specified status ids (if any)
            if (!empty($options['user_id']))
                $select->where('sb.user_id in (?)', $options['user_id']);

            // filter results on specified status ids (if any)
            if (!empty($options['email_address']))
                $select->where('sb.email_address in (?)', $options['email_address']);

            // filter results on specified status ids (if any)
            if (!empty($options['remote_ip']))
                $select->where('sb.remote_ip in (?)', $options['remote_ip']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
    }