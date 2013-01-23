<?php

/**
 * Yehoodi 3.0 DiscussionController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Handles the Discussion page actions 
 *
 */
class DiscussionController extends CustomControllerAction
{
	
    // Vars for pagination
	protected $limit;
	protected $adjacents;
	protected $page;
	protected $offset;
	protected $userLat = null;
	protected $userLon = null;
	protected $userLastLogin = null;
	
	public $action;
	public $categoryUrl;
	public $categoryText;
	public $range;
	public $order;
	public $rsrcId;
	public $catId;
	public $rsrc_type_id;
	public $viewType;
	public $eFilter;
	
	// Vars for populating the category list
	public $categoryList;
	
	public $resourceOptions;
	
	protected $numExpandedResources;
	protected $numCollapsedResources;
		
	public function init()
	{
        parent::init();
        $request = $this->_request;
        $discussionBar = null;
        
        // get our user (if logged in)
        if ($this->identity = Zend_Auth::getInstance()->getIdentity()) {
			$user = new DatabaseObject_User($this->db);
			$user->load($this->identity->user_id);
			
			// get user's discussion bar config
			$discussionBar = str_replace('/browse','/discussion',$user->profile->browse_bar);
			$discussionView = $user->profile->browse_view;
			$discussionFilter = $user->profile->browse_filter;
			
			// get user's location info
			$this->userLat = $user->profile->latitude;
			$this->userLon = $user->profile->longitude;
			$this->userUnit = $user->profile->unit;
			$this->userDistance = DatabaseObject_Resource::getDistance($user->profile->distance, $this->userUnit);
			$this->userLastLogin = $user->date_last_updated;
		} else {
		    $discussionView = 'normal';
		    $discussionFilter = 'all';
		}
		
		// Check for default URL in case the user set up one in her config
        // Nothing on the discussion bar
        if ($this->getRequest()->getRequestUri() == "/discussion/") {
			// check for discussion bar settings
			if($discussionBar) {
	        	$this->_redirect($discussionBar . '?view=' . $discussionView . '&eFilter=' . $discussionFilter);
			}
        }
        
        // Set up new date time
        $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
		$today = $dateTime->format("Y-m-d H:i:s");
		$todayShort = $dateTime->format("Y-m-d");
		
		// Get 7 days old date
		$dateTime->modify('-7 days');
		$last7Days = $dateTime->format("Y-m-d H:i:s");

		// Get 30 days old date
		$dateTime->modify('-23 days');
		$last30Days = $dateTime->format("Y-m-d H:i:s");

		// Get 90 days old date
		$dateTime->modify('-60 days');
        $last90days = $dateTime->format("Y-m-d H:i:s");
        
        // All time date
        $allTime = '1999-04-24 00:00:00';    // The first topic date on Yehoodi ever.
        
        // Get the page params
    	$this->action = $request->getParam('action');
    	$this->categoryUrl = $request->getParam('category');
    	$this->order = $request->getParam('order');
    	$this->range = $request->getParam('range');
    	$this->page = $request->getParam('page');
    	
		// Make sure only moderators are trying to get into the ADMIN pages
		if ($this->action == 'admin' && !$this->identity->mod) {
	        $this->_redirect(Zend_Registry::get('serverConfig')->location . 'discussion/all/all/30days/date/?view=normal');
		}
		
	    // This is handling any invalid ranges from the old discussion page
    	switch ($this->range)
		{
		    // The following are invalid ranges for the new discussion page
		    case 'activity':
		    case 'date':
		    case 'comment':
		    case 'views':
		    case 'popular':
		        $this->_redirect(Zend_Registry::get('serverConfig')->location . 'discussion/all/all/30days/date/?view=normal');
		        break;
		}

		if ($this->action != 'bookmarks') {
        	$this->rsrc_type_id = DatabaseObject_ResourceType::getResourceTypeIdByUrl($this->db, $this->action);
            $this->catId = DatabaseObject_Category::getCatIdByUrl($this->db, $this->categoryUrl);
            $this->categoryText = DatabaseObject_Category::getCategoryTextByUrl($this->db, $this->categoryUrl);
            if ($this->range == 'all') {
                $this->range = '30days';
            }
        }

    	// special case 
    	if ($this->range != 'all' && $this->action == 'bookmarks') {
    		$this->range = 'all';	// clear the order so it goes to the default
    	}

		// pagination from config ini
        $this->numExpandedResources = Zend_Registry::get('paginationConfig')->ResourcesPerPage;
		$this->numCollapsedResources = Zend_Registry::get('paginationConfig')->CollapsedResourcesPerPage;
		
		// Discussion Bar
		// set the default options for getting the list of categories for the template
        $categoryTypeOptions = array(
            'order'		=> array('rt.rsrc_type_id', 'c.order')	// order id column
        );
		
		// get the list of categories.
        $this->categoryList = DatabaseObject_Category::getCategoriesAndResources($this->db, $categoryTypeOptions);
    	
		// simple check for the page format
		// Normal or Collapsed
		if ($request->getParam('view') == 'collapsed') {
			$this->viewType = 'collapsed';
	    	//$this->limit = $this->numCollapsedResources;
	    	//$this->view->numCollapsedResources = $this->numCollapsedResources;
		} else {
			$this->viewType = 'normal';
	    	//$this->limit = $this->numExpandedResources;
	    }
	    
	    // Set up the limit of topics per page (normal or collapsed)
	    if ($this->viewType == 'normal') {
	    	$this->limit = $this->numExpandedResources;
	    } else {
	    	$this->limit = $this->numCollapsedResources;
	    	$this->view->numCollapsedResources = $this->numCollapsedResources;
	    }

		// check for Local, All events. This must be set.
		if ($request->getParam('eFilter') == 'local') {
    		    $this->eFilter = 'local';
		} else {
		    $this->eFilter = 'all';
		}
		
		// If it's local then set the location in the SQL
		if ($this->eFilter == 'local' && $this->action == 'event') {
    		$distance = DatabaseObject_Resource::mysqlHaversine($this->userLat,
    															$this->userLon,
    															$this->userDistance,
    															$this->userUnit );
		}
		
		$this->view->numCollapsedResources = $this->numCollapsedResources;
    	$this->view->numExpandedResources = $this->numExpandedResources;
    	$this->adjacents = Zend_Registry::get('paginationConfig')->Adjacents;
        $this->offset = ($this->page - 1) * $this->limit;

        //
        // Date Range
        //

        // Defaults start here
        $options = array();
        $options['rsrc_type_id'] = $this->rsrc_type_id;

        if ($this->action != 'event' && $this->action != 'bookmarks') {
            $options['exclude_cat_id'] = array(9,10,11,13,14);
        }
        
        switch ($this->range) {
            case 'lastvisit':
                // User last visit date
				$options['rsrc_date'] = $this->userLastLogin;
				$options['last_visit_user_id'] = $this->identity->user_id;  // For memcache
                break;
                
            case '7days':
                // user wants last 7 days
				$options['rsrc_date'] = $last7Days;
                break;
                
            case '30days':
                // user wants last 30 days
				$options['rsrc_date'] = $last30Days;
                break;
                
                // user wants last 365 days
            case '90days':
				$options['rsrc_date'] = $last90days;
                break;
                
            case 'allTime':
                // We are only allowing an 'allTime' range for logged in users
                if (is_object($this->identity)) {
    				$options['rsrc_date'] = $allTime;
    				$options['order'] = $this->order;
                } else {
    				$options['rsrc_date'] = $last30Days;
    				$options['order'] = $this->order;
                    $this->range = '30days';
                }
                break;
                
            default:
                // Default to last 30 days
				$options['rsrc_date'] = $last30Days;
                break;
        }
        
        //
        // Ordering
        //

        $resultIds = array();
        
        // If these are bookmarks then we are pulling
        // from a specific set of rsrc_ids and not the whole db
		if ($this->action == 'bookmarks') {
		    $options['user_key'] = $this->identity->user_id;
    		$resultIds = DatabaseObject_UserBookmark::getAllBookmarksByUserId($this->db, array('user_id' => $this->identity->user_id));
    		
    		if (count($resultIds)) {
    		    $options['rsrc_id'] = $resultIds;
    		}
		}

		// if we have a cat_Id on url
		if($this->catId) {
			$options['cat_id'] = $this->catId;
		}

		switch ($this->order) {
        	case 'activity':		// sort by resource/comment activity (NOT date range specific)
        	    $order = 'date_last_active';
        		break;
        		
        	case 'date':			// sort by resource date (Topic Added)
        		$order = 'rsrc_date';
        		break;
        		
        	case 'popular':			// sorty by resource vote up number
        		$order = 'votes';
        		break;
        		
        	case 'views':			// sort by resource number of views
        		$order = 'views_lifetime';
        		break;
        		
        	case 'comment':			// sort by resource number of comments
        		$order = 'count_comments';
        		break;
        		        		
        	default:				// default sort is resource date
        		$order = 'rsrc_date';
        		$startDate = null;
        		break;
        }
		
		// set the default resource options
		$this->resourceOptions = array(
            'rsrc_id'		=> (empty($resultIds) ? $resultIds = null : $resultIds),				         // id or an array of ids to get
            'cat_id'		=> (empty($options['cat_id']) ? null : $options['cat_id']),						// Categoty ID
            'now_date'		=> $today,									// date range to pass into FROM
            //'to'			=> '',										// date range to pass into TO
            'limit'			=> $this->limit,							// limit number of records
            'offset'		=> $this->offset,							// records offet for pagination
            'order'			=> $order, 									// What are we ordering this result set by?
            'rsrc_date'		=> (empty($options['rsrc_date']) ? null : $options['rsrc_date']),	 // resource date
            'start_date'	=> (empty($startDate) ? $startDate = null : $startDate),					     // EVENT specific date range
            'after_date'	=> (empty($afterDate) ? $afterDate = null : $afterDate),					     // EVENT specific date range
            'distance'		=> (empty($distance) ? $distance = null : $distance),						     // EVENT specific distance
            'is_active'		=> DatabaseObject_Resource::STATUS_LIVE, 	// status of the resource, active or not
            'range'         => $this->range,
            'user_key'       => (empty($options['user_key']) ? null : $this->identity->user_id), 		// User id to filter on
            'action'        => $this->action,
            'last_visit_user_id'        => (empty($options['last_visit_user_id']) ? null : $options['last_visit_user_id'])
        );
        
        if ($this->order == 'activity') {
            $this->resourceOptions['date_last_active_start'] = $options['rsrc_date'];
            $this->resourceOptions['date_last_active_end'] = $today;
            unset($this->resourceOptions['rsrc_date']);
            unset($this->resourceOptions['now_date']);
        }

    } // init

	public function indexAction()
    {
		// Default page setup is all resources displayed
    	$this->_forward('all');
    }

	public function allAction()
	{
		// They want EVERYTHING!
		$this->render();
	}
    
	public function featuredAction()
	{
    	// They want FEATURED

    	// get the resource id from the config.ini
		$this->resourceOptions['rsrc_type_id'] = $this->rsrc_type_id;
		$this->render();
	}
    
	public function lindyAction()
	{
    	// They want LINDY

    	// get the resource id from the config.ini
		$this->resourceOptions['rsrc_type_id'] = $this->rsrc_type_id;
		$this->render();
	}
    
	public function eventAction()
	{
    	// They want EVENTS
    	
    	// get the resource id from the config.ini
		$this->resourceOptions['rsrc_type_id'] = $this->rsrc_type_id;
		$this->render();
	}
    
	public function loungeAction()
	{
    	// They want THE LOUNGE

    	// get the resource id from the config.ini
		$this->resourceOptions['rsrc_type_id'] = $this->rsrc_type_id;
		$this->render();
	}
    
	public function bizAction()
	{
    	// They want BIZ

    	// get the resource id from the config.ini
		$this->resourceOptions['rsrc_type_id'] = $this->rsrc_type_id;
		$this->render();
	}
    
	public function adminAction()
	{
    	// For moderators ONLY

    	// get the resource id from the config.ini
		$this->resourceOptions['rsrc_type_id'] = $this->rsrc_type_id;
		$this->render();
	}
    
	public function bookmarksAction()
	{
		if (!$this->identity = Zend_Auth::getInstance()->getIdentity()) {
        	$this->_redirect($this->getUrl('login','account'));
		}
    	// They want their bookmarks
    	
    	// This is a special case since if the user has no bookmarks then we don't run the
    	// resources query.
    	
    	$this->rsrc_type_id = 'bookmarks';
		$this->render();
	}

    /**
     * Renders the pages for browsing
     *
     */
    public function render()
    {
	    // Do regular topic type code
        //Zend_Debug::dump($this->resourceOptions);
	    
	    // was there a Category type chosen?
        if($this->catId) {
        	$this->resourceOptions['cat_id'] = $this->catId;
        }
        
        // get the results
        
        // Bookmarks check
        if ($this->action == 'bookmarks') {
            
            $this->resourceOptions['bookmark'] = 1;
            unset($this->resourceOptions['rsrc_date']);
            
            if (count($this->resourceOptions['rsrc_id']) < 1) {
                $totalResources = 0;
                $recentResources = array();
            } else {
                $result = DatabaseObject_Resource::getResources($this->db, $this->resourceOptions);
                $recentResources = $result['topics'];
                $totalResources = $result['count'];
            }
        
        // Special case for handling the All Time link on the discussion...
        } elseif ($this->range == 'allTime') {
            $this->resourceOptions['page'] = $this->page;
            
            $recentResources = DatabaseObject_Resource::getAllTime($this->db, $this->resourceOptions);
            $totalResources = DatabaseObject_Resource::getResourceCount($this->db, $this->resourceOptions);
        
        // All other date ranges...
        } else {
            $result = DatabaseObject_Resource::getResources($this->db, $this->resourceOptions);
            $recentResources = $result['topics'];
            $totalResources = $result['count'];
        }
        
        // Invalid page number check
		if ($totalResources > 0) {
        	if ($this->page > ceil($totalResources / $this->limit)) {
	        	$this->_redirect($this->getUrl(null,'discussion'));
	        }
		}
		
        // Assign to Smarty
		$this->view->categoryTypes = $this->categoryList;
		$this->view->selectedRsrc = $this->rsrc_type_id;

		// set the catId in the template to the default catId if we are on an "all" page
		if(!$this->catId) {
			$this->view->categoryId = DatabaseObject_Category::getDefaultCatIdByResourceTypeId($this->db, array('rsrc_type_id' => $this->rsrc_type_id));
		} else {
			$this->view->categoryId = $this->catId;
		}
		$this->view->categoryUrl = $this->categoryUrl;
		$this->view->categoryText = $this->categoryText;
		
		$this->view->pageResultNum = $this->limit;
		$this->view->resources = $recentResources;
        $this->view->totalResults = $totalResources;
        $this->view->pageNumber = $this->page;
        
        $this->view->viewType = $this->viewType;
        $this->view->eFilter = $this->eFilter;
        $this->view->action = $this->action;

	    // send messages to the user
    	$this->view->messages = $this->_helper->_flashMessenger->getMessages();

    	// save the discussion url so the user comes back to this page
        $this->saveDiscussionUrl();
        
        $this->view->order = $this->order;
        $this->view->range = $this->range;
        
        $queryString = $this->viewType;
        if ($this->eFilter) {
            $queryString .= '&eFilter=' . $this->eFilter;
        }
        
        if ($totalResources > $this->limit) {
	        $this->getPaginationString($this->page, $totalResources, $this->limit, $this->adjacents, '',"{$this->getUrl(null,'discussion')}{$this->action}/{$this->categoryUrl}/{$this->range}/{$this->order}/", $queryString);
        }

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Discussion ' . ucwords($this->categoryText) . ' (page ' . $this->page . ')', $this->getUrl( null, 'discussion'));
            
        // Render!
        $this->_helper->viewRenderer('index');
    }

    /**
     * Saves the discussion url to the users
     * profile so the next time they hit Discussion
     * it starts where it left off.
     *
     */
	protected function saveDiscussionUrl()
    {
    	// save the discussion url so the user comes back to this page
		if ($this->identity = Zend_Auth::getInstance()->getIdentity()) {
			$user = new DatabaseObject_User($this->db);
			$user->load($this->identity->user_id);
			
			$url = "{$this->getUrl(null,'discussion')}{$this->action}/{$this->categoryUrl}/{$this->range}/{$this->order}/";
			$user->profile->browse_bar = $url;
			
			// Break down the query params
			if ($this->viewType) {
    			$user->profile->browse_view = $this->viewType;
			}
			
			if ($this->eFilter) {
    			$user->profile->browse_filter = $this->eFilter;
			}
			
			$user->save();
		}
		
		// Now save it to the session for comment conveinence links

		// Save it to the session
	    $discussionBarSession =  new Zend_Session_Namespace('discussionBarSession');
	    $discussionBarSession->unsetAll();

	    if ($this->range) {
	      $discussionBarSession->range = $this->range;
	    }
	    
	    if ($this->order) {
	      $discussionBarSession->order = $this->order;
	    }

	    if ($this->viewType) {
	      $discussionBarSession->viewType = $this->viewType;
	    }

	    if ($this->userLastLogin) {
	      $discussionBarSession->userLastLogin = $this->userLastLogin;
	    }
		
	    if ((!empty($this->resourceOptions['distance']))) {
	      $discussionBarSession->distance = $this->resourceOptions['distance'];
	    }
		
	    if ((!empty($this->resourceOptions['rsrc_type_id']))) {
	      $discussionBarSession->rsrcTypeId = $this->resourceOptions['rsrc_type_id'];
	    }
		
	    if ((!empty($this->resourceOptions['cat_id']))) {
	      $discussionBarSession->catId = $this->resourceOptions['cat_id'];
	    }
	    
	    if ($this->getRequest()->action == 'bookmarks') {
	      $discussionBarSession->bookmarks = true;
	    }
    }
        // Sample message
/*        $this->messenger->addMessage(array('error' => array('This is a sample error message 1',
        													'This is a sample error message 2')));
        $this->messenger->addMessage(array('notify' => array('This is a sample notify message',
        													 'This is sample notify message 2')));
        $this->messenger->addMessage(array('warning' => array('This is a sample warning message',
        													  'This is sample warning message 2')));
        $this->view->messages = $this->messenger->getMessages();
*/
        //Zend_Debug::dump($recentResources);
    
}