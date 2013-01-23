<?php
    class DatabaseObject_ResourceUrl extends DatabaseObject
    {

    	public function __construct($db)
        {
            parent::__construct($db, 'resource_url', 'url_id');

            // These are required
            $this->add('rsrc_id');
            $this->add('rsrc_url');
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
    }