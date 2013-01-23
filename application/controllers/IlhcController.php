<?php
/**
 * Yehoodi 3.0 IlhcController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * ILHC Controller 
 *
 */
class IlhcController extends CustomControllerAction
{
    public function init()
    {
        parent::init();
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('ILHC', $this->getUrl( null, 'ilhc'));

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