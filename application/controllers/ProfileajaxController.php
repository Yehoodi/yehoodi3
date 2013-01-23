<?php

/**
 * Yehoodi 3.0 ProfileAjaxController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class ProfileajaxController extends CustomControllerAction 
{
	public $Id;
	protected $ajaxPass;

	public function init()
	{
        parent::init();
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // Create ajax password
        $this->ajaxPass = PasswordManager::getInstance();
        $this->ajaxPass->createPassword();
	} // init

	
	/**
	 * Ajax method for toggling a
	 * ignoreed resource
	 *
	 */
	public function ignoreAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {
		
			$memberId = (int) str_replace('a_ignore_','',$this->_request->getParam('member_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('ignored_user_id'	=>	$memberId,
	        				 'user_id'			=>	$user_id
	        			);
	        
	        //Zend_Debug::dump($options);die;
	        			
	        if ($ignoreId = DatabaseObject_UserIgnore::getIgnore($this->db, $options)) {
	        	// We got a ignore, so we must delete it
	        	$ignore = new DatabaseObject_UserIgnore($this->db);
	        	$ignore->load($ignoreId);
	        	$ignore->delete();
	        	
	        	$ignored = "false";
	        } else {
	        	// Nothing was returned so it's a new ignore
	        	$ignore = new DatabaseObject_UserIgnore($this->db);
	        	$ignore->user_id = $user_id;
	        	$ignore->ignored_user_id = $memberId;
	        	$ignore->save();
	
	        	$ignored = "true";
	        }
	        
	        $output = $ignored;
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

}