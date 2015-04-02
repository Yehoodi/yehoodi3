<?php

/**
 * Yehoodi 3.0 RegisterController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * The site is Register
 *
 */
class RegisterController extends CustomControllerAction
{
    public function indexAction()
    {
        // Register message:
        $this->view->message = Zend_Registry::get('serverConfig')->RegisterMessage;

        $this->_helper->viewRenderer('index');
    }
}