<?php
    class DatabaseObject_CommentTracking extends DatabaseObject
    {

    	/**
    	 * Constructor:
    	 * 
    	 * Defines fields in the db
    	 * Instantiates a DateTime class
    	 * 
    	 * @param object $db
    	 */
        public function __construct($db)
        {
            parent::__construct($db, 'comment_tracking', 'track_id');

        	// Set up new date time
            $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));

            // These are required
            $this->add('user_id');
            $this->add('rsrc_id');
            $this->add('comment_num');
            $this->add('comment_user_id');
            $this->add('date_last_updated', $dateTime->format("Y-m-d H:i:s"));
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
        	return true;
        }

        /**
         * Get the track_id
         * for this user and resource_id
         * 
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getTrackingId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('ct.track_id')
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
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
                'cat_id' => array(),
                'from'    => '',
                'to'      => ''
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('ct' => 'comment_tracking'), array());

            // filter results on specified user ids (if any)
            if  (isset($options['user_id'])) {
                $select->where('ct.user_id = ?', $options['user_id']);
            }

            // filter results on specified resource ids (if any)
            if (isset($options['rsrc_id'])) {
                $select->where('ct.rsrc_id = ?', $options['rsrc_id']);
            }

            // filter results on specified r.rsrc_date (if any)
            if (isset($options['date_last_updated'])) {
                $select->where('ct.date_last_updated >= ?', $options['date_last_updated']);
            }
            
            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
        
    }