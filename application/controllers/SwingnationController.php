<?php
/**
 * Yehoodi 3.0 SwingnationController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Swingnation Controller 
 *
 */
class SwingnationController extends CustomControllerAction
{
    public function init()
    {
        parent::init();
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Swingnation', $this->getUrl( null, 'Swingnation'));

        // Get request
        $request = $this->_request;
        $this->page = $request->page;
    
    } // init

    public function indexAction()
    {
        switch($this->page) {
            default:
                $this->_helper->viewRenderer('index');
                break;
        }
    }
}