<?php
    /**
     * handles the user registration
     *
     */
	class FormProcessor_UserRegistration extends FormProcessor
    {
        protected $db = null;
        public $user = null;
        protected $_validateOnly = false;

        public function __construct($db)
        {
            parent::__construct();
            $this->db = $db;
            // Instantiate new user object
            $this->user = new DatabaseObject_User($db);
            $this->user->type = 2; // member type. TODO: get this from a config file
        }

        public function validateOnly($flag)
        {
            $this->_validateOnly = (bool) $flag;
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            // validate the user_name
            $this->user_name = trim($request->getPost('user_name'));

            if (strlen($this->user_name) == 0)
                $this->addError('user_name', 'Please enter a user name');
            else if (!DatabaseObject_User::IsValidusername($this->user_name))
                $this->addError('user_name', 'Please enter a valid user name');
            else if ($this->user->usernameExists($this->user_name))
                $this->addError('user_name', 'The selected user name already exists');
            else if (strlen($this->user_name) > 25)
                $this->addError('user_name', 'Please keep the user name 25 characters or less.');
            else
                $this->user->user_name = $this->user_name;

            // validate the user's name
            $this->first_name = $this->sanitize($request->getPost('first_name'));
            if (strlen($this->first_name) == 0)
                $this->addError('first_name', 'Please enter your first name');
            else if (strlen($this->first_name) > 25)
                $this->addError('first_name', 'Please keep the first_name 25 characters or less.');
            else
                $this->user->profile->first_name = $this->first_name;

            $this->last_name = $this->sanitize($request->getPost('last_name'));
            if (strlen($this->last_name) == 0)
                $this->addError('last_name', 'Please enter your last name');
            else if (strlen($this->last_name) > 25)
                $this->addError('last_name', 'Please keep the last name 25 characters or less.');
            else
                $this->user->profile->last_name = $this->last_name;

            // validate the e-mail address
            $this->email_address = $this->sanitize($request->getPost('email_address'));
            $validator = new Zend_Validate_EmailAddress();

            if (strlen($this->email_address) == 0)
                $this->addError('email_address', 'Please enter your e-mail address');
            else if (!$validator->isValid($this->email_address))
                $this->addError('email_address', 'Please enter a valid e-mail address');
            else if ($this->user->emailExists($this->email_address))
                $this->addError('email_address', 'The selected email address already exists');
            else
                $this->user->email_address = $this->email_address;

            // validate CAPTCHA phrase

            $session = new Zend_Session_Namespace('captcha');
            $this->captcha = $this->sanitize($request->getPost('captcha'));

            if ($this->captcha != $session->phrase) {
                $this->addError('captcha', 'Please enter the correct word');
            }
            
            // It don't mean a thing if it aint got that...
            $this->question = strtolower($this->sanitize($request->getPost('question')));

            if (strlen($this->question) == 0 || $this->question != 'swing') {
                $this->addError('question', 'Please finish the song title...');
            }

            // if no errors have occurred, save the user
            if (!$this->_validateOnly && !$this->hasError()) {
                $this->user->save();
                unset($session->phrase);
				
                // log this new user
	            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
	                $message = sprintf('New user %s added from %s.',
		                               $this->user_name,
		                               $_SERVER['REMOTE_ADDR']);
		
		            $logger = Zend_Registry::get('userlogger');
		            $logger->notice($message);
	            }
            }

            // return true if no errors have occurred
            return !$this->hasError();
        }
    }