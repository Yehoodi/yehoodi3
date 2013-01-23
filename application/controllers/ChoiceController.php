<?php

/**
 * April Fools joke
 *
 */
class ChoiceController Extends CustomControllerAction
{
    public function indexAction()
    {
        header('Location: /');
    	// Render!
        //$this->_helper->viewRenderer('index');
    }
}