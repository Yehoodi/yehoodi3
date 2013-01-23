<?php
    class Extended_Resource extends Extended 
    {
        public function __construct($db, $rsrc_id = null)
        {
        	parent::__construct($db, Zend_Registry::get('dbTableConfig')->tblResourceExtended);

            if ($rsrc_id > 0)
                $this->setResourceId($rsrc_id);
        }

        public function setResourceId($rsrc_id)
        {
            $filters = array('rsrc_id' => (int) $rsrc_id);
            $this->_filters = $filters;
        }
    }