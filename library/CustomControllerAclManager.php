<?php
/**
 * Yehoodi 3.0 CustomControllerAclManager Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Controls access levels and authorization
 *
 * 
 */
    
class CustomControllerAclManager extends Zend_Controller_Plugin_Abstract
    {
        // default user role if not logged or (or invalid role found)
        //private $_defaultRole = 'guest';
		//TODO: Get this from the db?
        private $_admin = 1;
		private $_member = 2;
		private $_guest = 3;

        // the action to dispatch if a user doesn't have sufficient privileges
        private $_authController = array('controller' => 'account',
                                         'action' => 'login');

        public function __construct(Zend_Auth $auth)
        {
            $this->auth = $auth;
            $this->acl = new Zend_Acl();

            // add the different user roles
            $this->acl->addRole(new Zend_Acl_Role($this->_guest));
            $this->acl->addRole(new Zend_Acl_Role($this->_member));
            $this->acl->addRole(new Zend_Acl_Role($this->_admin), $this->_member);

            // add the resources we want to have control over
            $this->acl->add(new Zend_Acl_Resource('discussion'));
            $this->acl->add(new Zend_Acl_Resource('discussionajax'));
            $this->acl->add(new Zend_Acl_Resource('submit'));
            $this->acl->add(new Zend_Acl_Resource('submitajax'));
            $this->acl->add(new Zend_Acl_Resource('profile'));
            $this->acl->add(new Zend_Acl_Resource('profileajax'));
            $this->acl->add(new Zend_Acl_Resource('mail'));
            $this->acl->add(new Zend_Acl_Resource('mailajax'));

            $this->acl->add(new Zend_Acl_Resource('comment'));
            $this->acl->add(new Zend_Acl_Resource('commentajax'));
            $this->acl->add(new Zend_Acl_Resource('detail'));
            $this->acl->add(new Zend_Acl_Resource('index'));

            $this->acl->add(new Zend_Acl_Resource('account'));
            $this->acl->add(new Zend_Acl_Resource('admin'));


            // allow access to everything for all users by default
            // except for the following pages (must be at least a member)
            $this->acl->allow();
            
            
            $this->acl->deny(null, 'account');
            $this->acl->deny(null, 'submit');
            $this->acl->deny(null, 'mail');
            $this->acl->deny(null, 'admin');
            $this->acl->deny($this->_guest, 'submitajax', array('locations',
            													   'ajaxselectcategory',
            													   'ajaxtitlematch',
            													   'locationsmanage'
            													   )
            				);

            $this->acl->deny($this->_guest, 'discussionajax', array('bookmark',
            													   'vote',
            													   'notify',
            													   'calendar',
            													   'savediscussionbar'
            													   )
            				);
            				
            $this->acl->deny($this->_guest, 'commentajax', array('showreplybox',
            													   'showeditbox',
            													   'updatereplyusername',
            													   'updatereplycomment',
            													   'updatequotedcomment',
            													   'updateresourceusername'
            													   )
            				);

            $this->acl->deny($this->_guest, 'mailajax', array('deletemail',
            													   'markasread',
            													   'markasnew'
            													   )
            				);

            $this->acl->deny($this->_guest, 'profileajax', array('ignore'
            													   )
            				);

            // add an exception so guests can log in or register
            // in order to gain privilege
            $this->acl->allow($this->_guest, 'account', array('login',
                                                        'fetchpassword',
                                                        'register',
                                                        'registercomplete'));

            $this->acl->allow($this->_guest, 'profile', array('index'));

            $this->acl->allow($this->_guest, 'comment', array('index',
                                                        'render'));

            // allow members access to the comment area
            $this->acl->allow($this->_member, 'comment');

            // allow members access to the account management area
            $this->acl->allow($this->_member, 'account');

            // allows members access to the submit area
            $this->acl->allow($this->_member, 'submit');

            // allows members access to the profile area
            $this->acl->allow($this->_member, 'profile');

            // allows members access to the mail area
            $this->acl->allow($this->_member, 'mail');

            // allows administrators access to the admin area
            $this->acl->allow($this->_admin, 'admin');

        }

        /**
         * preDispatch
         *
         * Before an action is dispatched, check if the current user
         * has sufficient privileges. If not, dispatch the default
         * action instead
         *
         * @param Zend_Controller_Request_Abstract $request
         */
        public function preDispatch(Zend_Controller_Request_Abstract $request)
        {
            // check if a user is logged in and has a valid role,
            // otherwise, assign them the default role (guest)
            if ($this->auth->hasIdentity())
                $role = $this->auth->getIdentity()->user_type;
            else
                $role = $this->_guest;

            if (!$this->acl->hasRole($role))
                $role = $this->_guest;

            // the ACL resource is the requested controller name
            $resource = $request->controller;

            // the ACL privilege is the requested action name
            $privilege = $request->action;

            // if we haven't explicitly added the resource, check
            // the default global permissions
            if (!$this->acl->has($resource))
                $resource = null;

            // access denied - reroute the request to the default action handler
            if (!$this->acl->isAllowed($role, $resource, $privilege)) {
                $request->setControllerName($this->_authController['controller']);
                $request->setActionName($this->_authController['action']);
            }
        }
    }
?>