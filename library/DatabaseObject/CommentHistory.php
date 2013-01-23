<?php
    class DatabaseObject_CommentHistory extends DatabaseObject
    {

        protected $dateTime;

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
            parent::__construct($db, 'comment_history', 'edit_id');

        	// Set up new date time
            $this->dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));

            // These are required
            $this->add('comment_id');
            $this->add('rsrc_id');
            $this->add('editor_id');
            $this->add('date_edited', $this->dateTime->format("Y-m-d H:i:s"));
            $this->add('title');
            $this->add('comment');
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

        public static function getRevisionHistory($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null,
            				array(  'ch.date_edited',
									'ch.title',
									'ch.comment',
									'u.user_name'
									)
								)
            	   ->join(array('u' => 'user'),
            			  'u.user_id = ch.editor_id',
            			   		array()
            			 );

            $select->order($options['order']);

            //Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);

            return $data;
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
            $select->from(array('ch' => 'comment_history'), array());


            // filter results on specified comment ids (if any)
            if (isset($options['comment_id']))
                $select->where('ch.comment_id = ?', $options['comment_id']);

            // filter results on specified resource ids (if any)
            if (isset($options['rsrc_id']))
                $select->where('ch.rsrc_id = ?', $options['rsrc_id']);

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }
        
    }