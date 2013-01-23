<?php
    class FormProcessor_SiteDisallow extends FormProcessor
    {
        protected $db = null;
        protected $disallow = null;
        
        public function __construct($db, $disallowId)
        {
            parent::__construct();

            $this->db = $db;
            
            $this->disallow = new DatabaseObject_SiteDisallow($db);
            $this->disallow->load($disallowId);
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            
            $this->user_name =			$this->sanitize($request->getPost('ban_user_name'));
        	
    		// Check for any fields
        	if($this->user_name == '') {
        		return;
        	} elseif (DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $this->user_name))) {
        		$this->addError('ban_user_name','The username already exists in the database and cannot be disallowed.');
        	} elseif (DatabaseObject_SiteDisallow::getDisallowNames($this->db, array('user_name' => $this->user_name))) {
        		$this->addError('ban_user_name','The username is already being disallowed.');
        	}

	        $this->disallow->user_name = $this->user_name;

            // if no errors have occurred, save the user
            if (!$this->hasError()) {
                $this->disallow->save();
            }

            // return true if no errors have occurred
            return !$this->hasError();
        }
    }