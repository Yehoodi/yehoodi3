<?php
    class DatabaseObject_MailBody extends DatabaseObject
    {

        public $meta = null;
        public $neatMailBodyDate = null;
        public $avatar;
        
        protected $dateTime;
        
        const STATUS_DRAFT = 0;
        const STATUS_LIVE = 1;

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
            parent::__construct($db, 'mail_body', 'mail_id');

            // These are required
            $this->add('mail_body');
            
        }

        protected function preInsert()
        {
        	return true;
        }

        protected function postLoad()
        {
        	$this->mail_body = html_entity_decode($this->mail_body);
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
         * Gets the last mail_id from the mail_body table
         *
         * @param object $db
         * @param int $id
         */
        public static function getLastMailBodyId($db)
        {
        	$options = array(
        		'offset'	=> 0,
        		'limit'		=> 1,
        		'order'		=> 'mail_id DESC'
        	);
            // instantiate a Zend select object
            $select = $db->select();
            
            // Build query
            $select->from('mail_body',array('mail_id'))
                   ->order($options['order'])
				   ->limit($options['limit'], $options['offset']);
					
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchOne($select);
            
            return $result;
        }
        
        /**
         * Gets the last mail_body 
         * by the mail id
         *
         * @param object $db
         * @param int $id
         */
        public static function getMailBodyIdByMailId($db, $id)
        {
        	$options = array(
        		'offset'	=> 0,
        		'limit'		=> 1,
        		'order'		=> ''
        	);
            // instantiate a Zend select object
            $select = $db->select();
            
            // Build query
            $select->from(array('mb' => 'mail_body'),
            				array('mail_body'))
            		->where('mb.mail_id = (?)', $id);
					
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchRow($select);
            
            return $result;
        }
        
        // ADMIN ONLY!!
        public static function getImgDump($db)
        {
        	$options = array(
        		'offset'	=> 0,
        		'limit'		=> 1,
        		'order'		=> ''
        	);
            // instantiate a Zend select object
            $select = $db->select();
            
            // Build query
            $select->from('mail_body',array('mail_body'))
            		->where('mail_body like "%[img%"');
					
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);
            
            return $result;
        }
        
    }