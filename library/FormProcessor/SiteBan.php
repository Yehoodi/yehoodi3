<?php
    class FormProcessor_SiteBan extends FormProcessor
    {
        protected $db = null;
        protected $ban = null;
        
        public function __construct($db, $banId)
        {
            parent::__construct();

            $this->db = $db;
            
            $this->ban = new DatabaseObject_SiteBan($db);
            $this->ban->load($banId);
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            
            $this->user_name =			$this->sanitize($request->getPost('user_name'));
            $this->email_address =		$this->sanitize($request->getPost('email_address'));
            $this->remote_ip =			$request->getPost('remote_ip');
        	
        	switch ($request->getParam('section')){
            	case 'ban':
            		// Check for any fields
		        	if($this->user_name == '' 
		        		&& $this->email_address == '' 
		        		&& $this->remote_ip == '') {
		        			
		        			return;
		        	}

            		// validate user name
		            if($this->user_name) {
	            		$userId = DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $this->user_name));
	            		
	            		if (!$userId) {
			                $this->addError('user_name', 'User not found.');
			            }
			            
			            $this->ban->user_id = $userId;
		            }
		
            		// validate email_address
		            $this->ban->email_address = $this->email_address;
		            
            		// validate remote_ip
		            // this is the user's current IP address
		        	//$ip = ip2long($_SERVER['REMOTE_ADDR']);
		        	
		        	$this->ban->remote_ip = ip2long($this->remote_ip);

		        	
            		break;
            		
            	case 'disallow':
            		// Validate Disallow Page
					break;
            	
            	case 'manage':	
            		// Validate Management Page
					break;
            		
            	case 'permission':	
            		// Validate Permissions Page
					break;
            		
            	default:
            		// Error screen
            		break;            		
            }
            
            // if no errors have occurred, save the user
            if (!$this->hasError()) {
                $this->ban->save();
            }

            // return true if no errors have occurred
            return !$this->hasError();
        }
    }