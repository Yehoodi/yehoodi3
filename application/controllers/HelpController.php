<?php
/**
 * Yehoodi 3.0 HelpController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Help and FAQ Controller 
 *
 */
class HelpController extends CustomControllerAction
{
    public function init()
    {
        parent::init();
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Help', $this->getUrl( null, 'help'));

        // Get request
        $request = $this->_request;
        $this->page = $request->page;
    
    } // init

    public function indexAction()
    {
        switch($this->page) {
            case 'privacy-policy':
            case 'faq':
            case 'terms-of-agreement':
            case 'colophon':
            case 'roadmap':
			case 'contact':
			    $this->view->section = $this->page;
                $this->_helper->viewRenderer($this->page);
                break;
            
            default:
                $this->_helper->viewRenderer('index');
                break;
        }
    }
}