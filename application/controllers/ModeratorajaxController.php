<?php

/**
 * Yehoodi 3.0 ModeratorAjaxController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class ModeratorajaxController extends CustomControllerAction 
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
	 * closeded resource
	 *
	 */
	public function closedAction()
	{
		if($this->identity->mod) {
		
			$resourceId = (int) str_replace('a_closed_','',$this->_request->getParam('rsrc_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('rsrc_id'	=>	$resourceId
	        			);
	        
        	$resource = new DatabaseObject_Resource($this->db);
        	$resource->load($resourceId);
			if($resource->closed == 1) {
				$status = 0;
				$output = "open";
			} else {
				$status = 1;
				$output = "closed";
			}
        	
        	$options = array('status'		=> $status,
        					 'rsrc_id'		=> $resourceId
        					 );
			
			$resource->setClosedStatus($this->db, $options);
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}


}