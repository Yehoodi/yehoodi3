<?php

/**
 * Yehoodi 3.0 UserController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * User Controller
 *
 */
class UserController extends CustomControllerAction 
{

	public function init()
	{
        parent::init();
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('User', $this->getUrl( null, 'user'));
        
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();
    	} // init

	public function indexAction()
    {
    	$request = $this->getRequest();
    	//Zend_Debug::dump($request);
    }
}