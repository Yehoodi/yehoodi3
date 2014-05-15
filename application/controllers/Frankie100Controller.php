<?php
/**
 * Yehoodi 3.0 Frankie100Controller Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * Frankie100 Controller
 *
 */
class Frankie100Controller extends CustomControllerAction
{
    public function init()
    {
        parent::init();
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Frankie100', $this->getUrl( null, 'Frankie100'));

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