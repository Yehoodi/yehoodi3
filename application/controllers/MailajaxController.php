<?php

/**
 * Yehoodi 3.0 MailAjaxController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class MailajaxController extends CustomControllerAction 
{
	//public $commentId;
	protected $ajaxPass;

	public function init()
	{
        parent::init();
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // Set up flash! aaaaahhhhhh!... messenger
		$this->messenger = $this->_helper->_flashMessenger;
	} // init

	/**
	 * Handles the ajax funcion from the mail page for deleting mail
	 *
	 */
	public function deletemailAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {
	        
			$threads = array();
			$threads = $this->_request->getParam('threads');
			
			if(count($threads)) {
		
				// Turns off automatic rendering to the template
				$this->_helper->viewRenderer->setNoRender();
		        
				$result = DatabaseObject_Mail::updateDeletedMail($this->db, $threads);
				//Zend_Debug::dump($threads);
				
				if($result) {
			        $this->messenger->addMessage(array('notify' => array('Conversation successfully deleted.')));
				} else {
			        $this->messenger->addMessage(array('error' => array('No mail deleted.')));
				}
			}			
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Handles the ajax funcion from the mail page
	 * for marking mail as read
	 *
	 */
	public function markasreadAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {
	        
			$threads = array();
			$threads = $this->_request->getParam('threads');
			
			if(count($threads)) {
				
				//Zend_Debug::dump($threads);die;
		
				// Turns off automatic rendering to the template
				$this->_helper->viewRenderer->setNoRender();
		        
				DatabaseObject_MailStatus::markAsRead($this->db, $threads);
				//Zend_Debug::dump($threads);
			}			
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Handles the ajax funcion from the mail page
	 * for marking mail as new
	 *
	 */
	public function markasnewAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {
	        
			$threads = array();
			$threads = $this->_request->getParam('threads');
			
			if(count($threads)) {

				//Zend_Debug::dump($threads);die;
	
				// Turns off automatic rendering to the template
				$this->_helper->viewRenderer->setNoRender();
		        
				DatabaseObject_MailStatus::markAsNew($this->db, $threads);
			}
						
		} else {
			die("Access Denied");
		}
	}

 }