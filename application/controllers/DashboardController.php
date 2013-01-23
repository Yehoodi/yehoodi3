<?php

/**
 * Yehoodi 3.0 DashboardController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Not much to see here. Redirects to the index
 * page since the Dashboard is no more...
 *
 */
class DashboardController extends CustomControllerAction 
{
	public function init()
	{
	}
	
	public function indexAction()
    {
    	$this->_redirect('/');
    }
}