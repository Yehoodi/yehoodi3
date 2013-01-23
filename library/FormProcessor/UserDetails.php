<?php
    class FormProcessor_UserDetails extends FormProcessor
    {
        protected $db = null;
        public $user = null;

        // This is our whitelist of allowed html tags for resources
        static $tags =     '<a>
                            <b>
                            <em>
                            <i>
                            <u>';
        
        public function __construct($db, $user_id)
        {
            parent::__construct();

            $this->db = $db;
            $this->user = new DatabaseObject_User($db);
            $this->user->load($user_id);

            // Details
            $this->email_address = 		$this->user->email_address;
            $this->first_name = 		$this->user->profile->first_name;
            $this->last_name = 			$this->user->profile->last_name;
            $this->website = 			$this->user->profile->website;
            $this->birthdate = 			$this->user->profile->birthdate;
            $this->gender = 			$this->user->profile->gender;
            $this->occupation= 			$this->user->profile->occupation;
            $this->interests= 			$this->user->profile->interests;
            
            $this->signature = 			$this->user->signature; // Just the raw data from the db
            $this->sigRaw =				$this->user->sigRaw;	// This is built in the postLoad() of the user object
            
            $this->utilize_email = 		$this->user->profile->utilize_email;
            $this->bio = 				$this->user->profile->bio;
            $this->avatar = 			$this->user->profile->avatar;
            $this->hometown = 			$this->user->profile->hometown;
            
			// Avatar
            $this->avatar= $this->user->profile->avatar;
            
            // Site Settings
			$this->filter_dirty = 		$this->user->profile->filter_dirty;
            $this->notify_by_email = 	$this->user->profile->notify_by_email;
            $this->user_invisible = 	$this->user->profile->user_invisible;
            $this->location = 			$this->user->profile->location;
            $this->unit = 				$this->user->profile->unit;
            $this->distance = 			$this->user->profile->distance;

            $this->num_posts   = 		$this->user->profile->num_posts;


		     //foreach ($this->publicProfile as $key => $label)
             //   $this->$key = $this->user->profile->$key;
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            
        	$action = $request->getParam('action');
            if ($action == 'details' || $action == 'users' ) {
            		// Validate Details Page

            		// validate first name
            		$this->first_name = $this->sanitize($request->getPost('first_name'));
//                    if (strlen($this->first_name) == 0) {
//		                $this->addError('first_name', 'Please enter your first name.');
//                    }
		            if (strlen($this->first_name) > 50) {
		                $this->addError('first_name', 'Can you shorten that first name? It is quite lenghty.');
		            }    
		            
		            $this->user->profile->first_name = $this->first_name;
		
            		// validate last name
		            $this->last_name = $this->sanitize($request->getPost('last_name'));
//                    if (strlen($this->last_name) == 0) {
//		                $this->addError('last_name', 'Please enter your last name.');
//                    }
		            if (strlen($this->last_name) > 50) {
		                $this->addError('last_name', 'Can you shorten that last name? It is quite lenghty.');
		            }

		            $this->user->profile->last_name = $this->last_name;
		
		            // check if a new password has been entered and if so validate it
		            $this->password = $this->sanitize($request->getPost('password'));
		            $this->password_confirm = $this->sanitize($request->getPost('password_confirm'));
		
		            if (strlen($this->password) > 0 || strlen($this->password_confirm) > 0) {
		                if (strlen($this->password) == 0)
		                    $this->addError('password', 'Please enter the new password');
		                else if (strlen($this->password_confirm) == 0)
		                    $this->addError('password_confirm', 'Please confirm your new password.');
		                else if ($this->password != $this->password_confirm)
		                    $this->addError('password_confirm', 'Please retype your password.');
		                else
		                    $this->user->password = $this->password;
		            }
		
					// validate the hometown
					$this->hometown = $this->sanitize($request->getPost('hometown'));
		            if (strlen($this->hometown) > 1024) {
		                $this->addError('hometown', 'Please shorten your hometown entry.');
		            }    

					$this->user->profile->hometown = $this->hometown;

					// validate the website
					$this->website = $this->sanitize($request->getPost('website'));
		            if (strlen($this->website) > 128) {
		                $this->addError('website', 'Please shorten your website entry to less than 128 characters.');
		            }    

					$this->user->profile->website = $this->website;

					// validate the birthdate
					$birthDateYear = $this->sanitize($request->getPost('birthDateYear'));
		            $birthDateMonth = $this->sanitize($request->getPost('birthDateMonth'));
		            $birthDateDay = $this->sanitize($request->getPost('birthDateDay'));
					if($birthDateYear && $birthDateMonth && $birthDateDay) {
			            if (checkdate($birthDateMonth, $birthDateDay, $birthDateYear)) {
				            $this->user->profile->birthdate = "{$birthDateYear}-{$birthDateMonth}-{$birthDateDay} 00:00:00";
						} else {
			                $this->addError('birthDate', 'That birthdate is not a valid date');
						}
					} else {
			            ($birthDateYear) ? '' : $birthDateYear = '1900';	// forced to set year to 1900 - we need a VALID year (not 0000)
			            ($birthDateMonth) ? '' : $birthDateMonth = '00';
			            ($birthDateDay) ? '' : $birthDateDay = '00';
						$this->user->profile->birthdate = "{$birthDateYear}-{$birthDateMonth}-{$birthDateDay} 00:00:00";
					}
					
					// validate the gender
					$this->user->profile->gender = $this->sanitize($request->getPost('gender'));
		            
					// validate the occupation
					$this->occupation = $this->sanitize($request->getPost('occupation'));
		            if (strlen($this->occupation) > 128) {
		                $this->addError('occupation', 'Please shorten your occupation entry to less than 128 characters.');
		            }    
		            
		            $this->user->profile->occupation = htmlspecialchars($this->occupation);
		            
					// validate the interests
					$this->interests = $this->sanitize($request->getPost('interests'));
		            if (strlen($this->interests) > 128) {
		                $this->addError('interests', 'Please shorten your interests entry to less than 128 characters.');
		            }    

		            $this->user->profile->interests = $this->interests;
		            
					// validate the sig
        			$this->sig = $request->getPost('sig');

        			if (strlen($this->sig) > 255) {
		                $this->addError('sig', 'Please shorten your sig entry to less than 255 characters.');
		            }    
					
		            $this->sig = $this->cleanHtml($this->sig, self::$tags);

		        	$this->user->profile->sig = htmlspecialchars($this->sig);
        			
					// validate the bio
					$this->bio = $this->sanitize($request->getPost('bio'));
		            if (strlen($this->bio) > 65535) {
		                $this->addError('bio', 'Please shorten your bio entry.');
		            }    
		            
					$this->user->profile->bio = $this->cleanHtml($this->bio, self::$tags);

					// validate the utilize_email
					$this->user->profile->utilize_email = $this->sanitize($request->getPost('utilize_email'));
		            
            }
            
            if ($action == 'settings' || $action == 'users' ) {
           		// Validate Settings Page
            		
					// validate the notify_by_email
					$this->user->profile->notify_by_email = $this->sanitize($request->getPost('notify_by_email'));
		            
					// validate the dirty word filter
					if($request->getPost('filter_dirty') == 'true' ) {
						$this->user->profile->filter_dirty = 'off';
					} else {
						unset($this->user->profile->filter_dirty);
					}

					// validate the invisible setting
					if($request->getPost('user_invisible') == 'true' ) {
						$this->user->profile->user_invisible = 'on';
					} else {
						unset($this->user->profile->user_invisible);
					}

					// validate the location
					$this->location = $this->sanitize($request->getPost('location'));
		            if (strlen($this->location) > 256) {
		                $this->addError('location', 'Please shorten your location entry to less than 128 characters.');
		            }    

					$this->user->profile->location = urldecode($this->location);

					// validate the long and lat
					$this->user->profile->latitude = $this->sanitize($request->getPost('latitude'));
					$this->user->profile->longitude = $this->sanitize($request->getPost('longitude'));
					
					// validate the unit
					$this->unit = $this->sanitize($request->getPost('unit'));
		            $this->user->profile->unit = $this->unit;

					// validate the distance
					$this->distance = $this->sanitize($request->getPost('distance'));
		            $this->user->profile->distance = $this->distance;

            }
            
            if ($action == 'avatar') {
					// validate the avatar
					$this->user->profile->avatar = $this->sanitize($request->getPost('avatar'));
            		
            }
            
            if ($action == 'users') {
            		// For ADMIN only
            	
            		// validate user type
            		$this->user_type = $request->getPost('select_userLevel');
		            if ($this->user_type > 3) {
		                $this->addError('select_userLevel', 'Invalid user level.');
		            }    
		            
		            $this->user->user_type = $this->user_type;
            		
            		// validate mod enabled
            		$this->mod = $request->getPost('select_mod');
		            if ($this->mod == 'true') {
			            $this->user->profile->mod = $this->mod;
		            } else {
			            $this->user->profile->mod = '';
		            }
		            
		            $this->user->profile->mod = $this->mod;
            		
            		// validate user name change
            		$this->user_name = $this->sanitize($request->getPost('user_name_change'));
		            if(strlen($this->user_name) > 0) {
	            		if (DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $this->user_name))) {
			                $this->addError('user_name_change', 'That username already exists. Cannot update.');
			            } else {
			            	$this->user->user_name = $this->user_name;
			            }
		            }
            		
            		// validate new password change
            		$this->password = $request->getPost('admin_password');
		            if (strlen($this->password) > 0) {

		                $this->user->password = $this->password;
		            }    
		            
            		
		            // validate the e-mail address
		            $this->email_address = $this->sanitize($request->getPost('email_address_change'));
		            $validator = new Zend_Validate_EmailAddress();
		
		            if (strlen($this->email_address) > 0) {
						if (!$validator->isValid($this->email_address))
			                $this->addError('email_address_change', 'Please enter a valid e-mail address');
			            else if ($this->user->emailExists($this->email_address))
			                $this->addError('email_address_change', 'The selected email address already exists');
			            else
			                $this->user->email_address = $this->email_address;
		            }
            		
            		// validate user level
            		$this->is_active = $this->sanitize($request->getPost('select_userActive'));
		            if ($this->is_active > 1) {
		                $this->addError('select_userActive', 'Invalid active level.');
		            }    
		            
		            $this->user->is_active = $this->is_active;
            }
            
            // if no errors have occurred, save the user
            if (!$this->hasError()) {
                $this->user->save();
            }

            // return true if no errors have occurred
            return !$this->hasError();
        }
    }