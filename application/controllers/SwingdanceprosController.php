<?php

/**
 * SwingdanceprosController
 * April Fools joke for 2011
 * 
 * @author
 * @version 
 */

class SwingdanceprosController extends Zend_Controller_Action {
	public function init() {
		// Init code...
	}
	
	public function indexAction() {
        //header('Location: /');
    	// Render!
        $this->_helper->viewRenderer('index');
	}

}
