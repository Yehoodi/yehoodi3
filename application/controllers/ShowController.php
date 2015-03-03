<?php

/**
 * Yehoodi 3.0 Show Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Controls all Yehoodi Shows actions
 *
 */
class ShowController extends CustomControllerAction 
{
	const SHOW_THE_TRACK           = 'TRK';
	const SHOW_HEY_MISTER_JESSE    = 'HMJ';
	const SHOW_YEHOODI_TALK        = 'YTS';
	const SHOW_YEHOODI_VIDEO       = 'YTV';
	const SHOW_YEHOODI_RADIO       = 'YRS';
	const SHOW_LINDYMAN            = 'LIN';
	const SHOW_SAUSAGEBEAVER       = 'SFBL'; // Sausage Fest / Beaver Lodge
	const SHOW_SWINGNATION		   = 'SWN';
	const SHOW_ILHC2012 		   = 'ILHA';
	const SHOW_ILHC2013 		   = 'ILHB';
	const SHOW_ILHC2014 		   = 'ILHB';

	public function init()
	{
        parent::init();
        $request = $this->_request;
        
        // Get the page params
    	$this->action = $request->getParam('action');
    	$this->episode = $request->getParam('episode');

    	// Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Show', $this->getUrl( null, 'show'));
        $this->view->smart_device = $this->is_smart_device();
        
        //Zend_Debug::dump($request->getParams());
	}

	public function indexAction()
    {
        // Render!
        $this->_helper->viewRenderer('index');
    }
    
    public function radioAction()
    {
        // Assign to smarty
        $this->view->showTitle = 'Yehoodi Radio';
        $this->view->showCode = self::SHOW_YEHOODI_RADIO ;
        $this->view->showURL = 'yehoodiradio';

        $this->renderPage();
    }

    public function yehooditalkshowAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_YEHOODI_TALK 
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'Yehoodi Talk Show';
        $this->view->showCode = self::SHOW_YEHOODI_TALK ;
        $this->view->showURL = 'yehooditalkshow?episode=';
        $this->view->feed = 'http://www.yehoodi.com/rss/yehooditalkshow.xml';

        $this->renderPage();
        
    }
    
    public function yehoodivideoAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_YEHOODI_VIDEO 
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'Yehoodi Talk Show Video Edition';
        $this->view->showCode = self::SHOW_YEHOODI_VIDEO ;
        $this->view->showURL = 'yehoodivideo?episode=';
        $this->view->feed = 'http://www.yehoodi.com/rss/yehoodivideo.xml';

        $this->renderPage();
        
    }
    
    public function heymisterjesseAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_HEY_MISTER_JESSE
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'Hey Mister Jesse Podcast';
        $this->view->showCode = self::SHOW_HEY_MISTER_JESSE ;
        $this->view->showURL = 'heymisterjesse?episode=';
        $this->view->feed = 'http://www.yehoodi.com/rss/heymisterjesse.xml';

        $this->renderPage();

    }

    public function thetrackAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_THE_TRACK
        );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'The Track - With Ryan Swift';
        $this->view->showCode = self::SHOW_THE_TRACK ;
        $this->view->showURL = 'thetrack?episode=';
        $this->view->feed = 'http://www.yehoodi.com/rss/thetrack.xml';

        $this->renderPage();

    }

    public function lindymanAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_LINDYMAN
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'The Adventures of Lindyman!';
        $this->view->showCode = self::SHOW_LINDYMAN ;
        $this->view->showURL = 'lindyman?episode=';
        //$this->view->feed = 'http://www.yehoodi.com/rss/heymisterjesse.xml';

        $this->renderPage();

    }
    
    public function sausagebeaverAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_SAUSAGEBEAVER 
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'Sausage Fest/Beaver Lodge';
        $this->view->showCode = self::SHOW_SAUSAGEBEAVER ;
        $this->view->showURL = 'sausagebeaver?episode=';
        $this->view->feed = 'http://www.yehoodi.com/rss/sausagebeaver.xml';

        $this->renderPage();
        
    }
	
	public function swingnationAction()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_SWINGNATION 
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'SwingNation';
        $this->view->showCode = self::SHOW_SWINGNATION ;
        $this->view->showURL = 'swingnation?episode=';
        $this->view->feed = 'http://www.yehoodi.com/rss/swingnation.xml';

        $this->renderPage();
    }

	public function ilhc2012Action()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_ILHC2012
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'International Lindy Hop Championships 2012 Live Coverage';
        $this->view->showCode = self::SHOW_ILHC2012 ;
        $this->view->showURL = 'ilhc2012?episode=';

        $this->renderPage();
    }

	public function ilhc2013Action()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_ILHC2013
                         );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'International Lindy Hop Championships 2013 Live Coverage';
        $this->view->showCode = self::SHOW_ILHC2013;
        $this->view->showURL = 'ilhc2013?episode=';

        $this->renderPage();
    }

    public function ilhc2014Action()
    {
        // Get the rsrc_ids for the shows
        $options = array('show_code' => self::SHOW_ILHC2014
        );
        $this->shows = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

        // Assign to smarty
        $this->view->showTitle = 'International Lindy Hop Championships 2014 Live Coverage';
        $this->view->showCode = self::SHOW_ILHC2014;
        $this->view->showURL = 'ilhc2014?episode=';

        $this->renderPage();
    }

    public function renderPage()
    {

        if($this->shows) {
            // Now get the resource objects themselves
            $options = array('rsrc_id'  => $this->shows,
                             'order'    => 'rsrc_date DESC',
                             'is_active'=> DatabaseObject_Resource::STATUS_LIVE
                             );
            $resources = DatabaseObject_Resource::getResourceById($this->db, $options);
            
            // Does the url suggest a particular episode?
            if(isset($this->episode)) {
            
                // See if we can grab that particular episode to show on top
                foreach($resources as $show) {
                    if($this->episode == $show->extended->show_episode) {
                        $currentShow = $show;
                    }
                }
            }
    
            // If I didn't get a match from the url's episode var, then it's invalid
            // and assign the current show to the latest show
            if(!$currentShow) {
                $currentShow = array_shift($resources);
            }

            $this->view->currentShow = $currentShow;
            $this->view->showArchive = $resources;
            $this->view->meta = strip_tags($currentShow->descrip);
        }

		// Assign our breadcrumb/page title
        $this->breadcrumbs->addStep($this->view->showTitle . ' - ' . $currentShow->extended->show_episode);

        // Render!
        $this->_helper->viewRenderer('show');

    }
}