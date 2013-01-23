<?php

/**
 * Yehoodi 3.0 ClosedController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * The site is closed
 *
 */
class ClosedController extends CustomControllerAction 
{
	
	public function init()
	{
	} // init

	public function indexAction()
    {
        // Closed message:
        $this->view->message = Zend_Registry::get('serverConfig')->closedMessage;	   
        
        // Render!
        //$this->_helper->viewRenderer('index');
        $this->_helper->viewRenderer('sopa');
    }
}