<?php

/**
 * Yehoodi 3.0 CommentController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Resource comments
 *
 */
class CommentController extends CustomControllerAction 
{

	// Search
	const SEARCH_SCORE = 2.0;
	const SEARCH_DAYS = 90;
	
	// Related & Featured result counts
	const FEATURED_ALL_LIMIT = 6;
	const FEATURED_SOME_LIMIT = 3;
	const RELATED_LIMIT = 3;

	// cookie size (for holding resource ids and view dates)
	const COOKIE_ARRAY_LIMIT = 150;
	
	// Vars for pagination
	protected $_resource;
	
	protected $limit;
	protected $adjacents;
	protected $page;
	protected $offset;
	
	public $rsrcId;
	public $rsrcUrl;
	public $resourceOptions;
	public $countOptions;

	public $action;
	public $order;
	
	public $commentId;

	public function init()
	{
        parent::init();
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // get any post/get info
    	$request = $this->getRequest();

    	$this->action = $request->action;
    	$this->rsrcId = $request->id;
    	$this->order =$request->order;
        $this->page = $request->page;
        $this->rsrcUrl = $request->url;
       	$this->commentId = $request->commentId;
        
        // This comes from the config.ini
    	$this->limit = Zend_Registry::get('paginationConfig')->CommentsPerPage;
    	$this->adjacents = Zend_Registry::get('paginationConfig')->Adjacents;
        $this->offset = ($this->page - 1) * $this->limit;

        // set the default record count options
		$this->countOptions = array(
            'rsrc_id'		=> $this->rsrcId,		// filtering on this resourceId
		);

        // set the default comment options
        $this->commentOptions = array(
            'rsrc_id'		=> $this->rsrcId,		// filtering on this resourceId
            'from'			=> '',					// date range to pass into FROM
            'to'			=> '',					// date range to pass into TO
            'limit'			=> $this->limit,		// limit number of records
            'offset'		=> $this->offset,		// records offet for pagination
            'order'			=> 'c.comment_num'		// What are we ordering this result set by?
        );

        // set the default resource options
        $this->resourceOptions = array(
            'rsrc_id' 		=> $this->rsrcId 		// resource id to filter on
        );

        // this checks if we are on an iPad / iPod / iPhone
        $this->view->smart_device = $this->is_smart_device();
        

	} // init

	public function indexAction()
    {
        // get the resource
		$this->_resource = DatabaseObject_Resource::getResourceById($this->db, $this->resourceOptions);
		
    	// Check if the url is active. If it's not, the public shouldn't be here, only the owner should.
        $redirect = false;
    	
        if (isset($this->identity->user_id)) {
            $owner = DatabaseObject_Resource::isOwner($this->db, array('rsrc_id' => $this->rsrcId,
            														  'user_id' => (int) $this->identity->user_id));
    	} else {
    	    $owner = false;
    	}
    	
        $activeStatus = DatabaseObject_Resource::getActiveStatus($this->db, array('rsrc_id' => $this->rsrcId));
    	
    	if (isset($this->identity->mod)) {
        	$redirect = false;
        } elseif ($activeStatus == DatabaseObject_Resource::STATUS_DRAFT && !$owner) {
            $redirect = true;
        } elseif ($activeStatus == DatabaseObject_Resource::STATUS_INACTIVE) {
            $redirect = true;
        }
        
        // Only mods are allowed in the following topic areas
        $modsOnly = array(
                        'admin'
                        );
        if (!isset($this->identity->mod) && in_array($this->_resource[$this->rsrcId]->meta->resourceName,$modsOnly)) {
        	$redirect = true;
        }

        if ($redirect) {
        	$this->_redirect('/');
        }
        
        // render
        $this->render();
    }

	public function render($action = null, $name = null, $noController = false)
	{
		// Get the paramaters from the request url
		$request = $this->getRequest();
		$validate = null;
		
		// Is this a post?
    	if($request->isPost()) {

	    	// Cancel Button
			if ($request->getPost('button_cancel') == "Cancel") {
	        	$this->_redirect($this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/'.$this->page);
	    		// Left the page...
			} 

    	}

    	// instantiate a new Comment object
    	$fp = new FormProcessor_Comment($this->db, $this->identity->user_id, $this->commentId);

		// get the related resources
		$relatedResources = $this->relatedResources($this->_resource[$this->rsrcId]->title);
				
		// get so get the featured resources
        if (!$relatedResources) {
            $featuredResources = DatabaseObject_Resource::getTopFeaturesIds($this->db, self::FEATURED_ALL_LIMIT );
        } else {
            $featuredResources = DatabaseObject_Resource::getTopFeaturesIds($this->db, self::FEATURED_SOME_LIMIT );
        }
        $options = array(
            'rsrc_id' 		=> $featuredResources, 		// resource ids to filter on
            'order'         => 'rsrc_date DESC'
        );
		$this->view->relatedResults = $relatedResources;
        $this->view->featuredResults = DatabaseObject_Resource::getResourceById($this->db, $options);
        		
		// get the comments
        $totalComments = DatabaseObject_Comment::GetCommentCount($this->db, $this->countOptions);
        $recentComments = DatabaseObject_Comment::getComments($this->db, $this->commentOptions);

        // Invalid page number check
		if ($totalComments > 0) {
        	if ($this->page > ceil($totalComments / $this->limit)) {
	        	$this->_redirect($this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/');
	        }
		}
        // Save if everything's cool
        if ($request->isPost() && $this->identity) {

        	// check if the request camefrom XMLHttpRequest (ajax) or not
	        $validate = $request->isXmlHttpRequest();

	        if ($validate) {
	    		// Only validate via Ajax
	            $fp->validateOnly(true);
	            $fp->process($request);
	        } 
	        else if ($fp->process($request)) {
				// TODO: Redirect to the last page in the thread or pop up a options screen
				// Calculate the last page in the thread to jump to
		        
				if ($this->commentId) {
					$currentComment = DatabaseObject_Comment::getCommentNumberByCommentId($this->db, array('comment_id' => $this->commentId));
					// Gotta get the current page number
					// And calculate the link back to the edited comment
					$currentPage = ceil($currentComment / $this->limit);

					header('Location: ' . $this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/'.$currentPage.'#comment_'.$currentComment);
					//$this->_redirect($this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/'.$currentPage.'#comment_'.$currentComment);
				} else {
					$totalComments = DatabaseObject_Comment::GetCommentCount($this->db, $this->countOptions);
					$lastPage = ceil($totalComments / $this->limit);
					$lastComment = $totalComments;
					
					header('Location: ' . $this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/'.$lastPage.'#comment_'.$lastComment);
		        	//$this->_redirect($this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/'.$lastPage.'#comment_'.$lastComment);
				}
			} else {
		        // Error on the form
				// User flash messneger to display the error on the current page
	        	$this->messenger = $this->_helper->_flashMessenger;
		        $this->messenger->addMessage(array('error' => $fp->getErrors()));
		        
		        // since we are leaving the controller right now, get the current messages
		        $this->view->messages = $this->messenger->getCurrentMessages();
	        	
		        //$this->_redirect($this->getUrl(null,'comment').$this->rsrcId.'/'.$this->rsrcUrl.'/'.$this->page.'#div_replyForm');
			}
        } elseif ($request->isPost() && !$this->identity) {
        	echo 'invalid user';
        	exit;
        }

		if ($validate) {
			if ($fp->getErrors()) {
				$json = array(
						'errors' => $fp->getErrors()
						);
			} else {
				//user's comment validates, allow the user to post
				$json = array();
			}
			$this->sendJson($json);
	    } else {
   	
			// Assign to Smarty
			$this->view->fp = $fp;
			$this->view->resources = $this->_resource;
			//Zend_Debug::dump($this->_resource);die;
			$this->view->resourceType = $this->_resource[$this->rsrcId]->meta->resourceName;
			$this->view->id = $this->rsrcId;
	
			// does this resource have event locations? (true or false)
			$this->view->locationCheck = $this->checkLocations($this->_resource);
			
	
			// Need the commentId to edit the comment
			$this->view->commentId = $this->commentId;
	
			// What did the user press?
			switch ($request->getPost('formAction')) {
				
				// Submit comment button
				case "Submit":
					// Don't put the commentId in the hidden field
					unset($this->commentId);
					break;
					
				// Preview comment button
				case "Preview":
					break;
			}
	
			if ($totalComments) {
				$this->view->pageResultNum = $this->limit;
				$this->view->comments = $recentComments;
		        $this->view->totalResults = $totalComments;
		        $this->view->pageNumber = $this->page;
		        
		        $this->view->order = $this->order;
		        if ($totalComments > $this->limit) {
			        $this->getPaginationString($this->page, $totalComments, $this->limit, $this->adjacents, '',"{$this->getUrl(null,'comment')}{$this->rsrcId}/{$this->rsrcUrl}/");
		        }
			} else {
		        $this->view->pageNumber = 1;	// We are at page 1 of comments
			}
	
			$this->view->rsrcUrl = $this->rsrcUrl;
	
            // Increase the view counter
            DatabaseObject_Resource::addView($this->db,$this->rsrcId);
            
            // set the view cookie
            $this->setViewTracking();
    
			// Assign our breadcrumb
	        $this->breadcrumbs->addStep($this->_resource[$this->rsrcId]->title . ' (page ' . $this->page . ')', $this->getUrl( null, 'comment'));

		    // send messages to the user
	    	$this->view->messages = $this->_helper->_flashMessenger->getMessages();
	    	
	    	// Adding site var for permalinking
	    	$this->view->location = Zend_Registry::get('serverConfig')->location;
	    	
	    	// Adding link back to calendar if coming from an event on the calnedar
	    	$this->showBack2CalendarLink();
	    	
	    	// conveinience links for previous and next topic
	    	$this->showPrevNextLinks($this->rsrcId);

	    	// Render!
	        $this->_helper->viewRenderer('index');
		
		}
	}
	
	/**
	 * Sets the smarty view for related resources
	 * if there are any.
	 *
	 * @param string $resource title
	 */
	public function relatedResources($resource)
	{
        // var
        $results = null;
        $maxRelated = 5;
        $limit = self::RELATED_LIMIT ;
		
        // Set up Sphinx Search Client
		$this->maxSearchResults = Zend_Registry::get('searchConfig')->sphinxMaxReturns;
        $sphinxIP = Zend_Registry::get('searchConfig')->sphinxLocation;
		$sphinxPort = (int) Zend_Registry::get('searchConfig')->sphinxPort;
		
        $this->sphinxClient = new SphinxClient();
        $this->sphinxClient->SetServer( $sphinxIP, $sphinxPort);
        $this->sphinxClient->SetConnectTimeout( 30 );

        $this->sphinxClient->SetLimits( 0, $limit, $maxRelated ); // How many records to pull

        $this->sphinxClient->SetFieldWeights(array('title' => 1000, 'description' => 10));    // Weights for field search
        $this->sphinxClient->SetSortMode( SPH_SORT_ATTR_DESC, 'date' );  // Sort with most recent stuff first. Cool!
		$this->sphinxClient->AddQuery( $resource, 'resources' );

    	$this->sphinxClient->SetArrayResult( true );    // Give me an array when done.
    
        // Run Query
        $searchResults = $this->sphinxClient->RunQueries();
        $searchResults = $searchResults[0];
        
        //Zend_Debug::dump($searchResults);
        
		// This needs to be a method somewhere...
        if ($searchResults['total_found']) {
			
			$resourceIds = array();
			$resourceOrder = array();
			$counter = 0;
		    foreach ($searchResults['matches'] as $id => $match) {
		        if ($match['weight'] > 100) {
    		        $resourceIds[] = $match['id'];
    		        $resourceOrder[$match['id']] = $counter++;
		        }
			}
		    
			if (count($resourceIds)) {
			    // Build the list of resources from the array
				$options = array('rsrc_id' => $resourceIds);
				
				// Get the resources from the db
			    $resources = DatabaseObject_Resource::getResourceById($this->db, $options);
			    
			    // Remove this resource from the related set
			    unset($resources[$this->rsrcId]);

			    // Add the order back into the array
			    $tmp = array();
			    foreach ($resources as $key => &$value) {
			        if (array_key_exists($key, $resourceOrder)) {
			            // Add the weight to the meta
			            $value->meta->order = $resourceOrder[$key];
			        }

			        // Set up temp array for sorting
			        $tmp[] = $value->meta->order;
			    }

			    // Re-sort the array for order DESC
			    array_multisort($tmp, $resources);

			    if (count($resources)) {
			        return $resources;
			    }
			    
			    return array();
			}
        }
	}
	
	
    /**
     * Creates an entry in the comment_tracking
     * table when a user views a comment thread.
     * 
     * Used to keep track of where the users have
     * left off when reading threads.
     *
     */
	protected function setViewTracking()
    {
		if ( $this->identity ) {
            $track = new DatabaseObject_CommentTracking($this->db);
	        $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
            
            $options = array('user_id'  => $this->identity->user_id,
                             'rsrc_id'  => $this->rsrcId);

            // any track_id already exist?
            $track_id = DatabaseObject_CommentTracking::getTrackingId($this->db, $options);
            
            $options = array('rsrc_id'      => $this->rsrcId
                            );
            if ( !$track_id ) {
                $track->user_id = $this->identity->user_id;
                $track->rsrc_id = $this->rsrcId;
                
                $trackingInfo = DatabaseObject_Comment::getTrackingInfo($this->db, $options);

                $track->comment_num = empty($trackingInfo['comment_num']) ? 0 : $trackingInfo['comment_num'];
                $track->comment_user_id = empty($trackingInfo['user_id']) ? 0 : $trackingInfo['user_id'];
            } else {
                $track->load($track_id);

                $trackingInfo = DatabaseObject_Comment::getTrackingInfo($this->db, $options);

                $track->comment_num = empty($trackingInfo['comment_num']) ? 0 : $trackingInfo['comment_num'];
                $track->comment_user_id = empty($trackingInfo['user_id']) ? 0 : $trackingInfo['user_id'];
                $track->date_last_updated = $dateTime->format("Y-m-d H:i:s");
            }
            
            $track->save();
		}        
    }
	
	/**
	 * Wrapper around getLocations to only check
	 * for locations on events
	 *
	 * @return bool
	 */
	protected function checklocations($resource)
	{
		if($resource[$this->rsrcId]->meta->resourceName == 'event' ) {
			
			return (bool) DatabaseObject_Location::getLocationCount($this->db, $this->rsrcId);
		}
	}

	/**
	 * Displays a back link to the calendar
	 * if coming from the calendar.
	 * 
	 * Only works for logged in users so far
	 *
	 */
	public function showBack2CalendarLink()
	{
	    $this->view->calendarLink =  null;

	    if (isset($_SERVER['HTTP_REFERER'])) {
    	    $url = parse_url($_SERVER['HTTP_REFERER']);
    
            if (!empty($url['path'])) {
                $params = explode('/', $url['path']);
                if ($params[1] == 'calendar') {
            	    if ($this->identity) {
            	        // Get link from the user's profile
        	            $user = new DatabaseObject_User($this->db);
        	            $user->load($this->identity->user_id);
        
            			// get user's calendar bar config
            			$calConfig = $user->profile->calendar_type . $user->profile->calendar_cat_loc;
            			if ($user->profile->calendar_year) {
            			    $calConfig .= '&year=' . $user->profile->calendar_year; // This is to set the week view to remember
            			}
            			if ($user->profile->calendar_month) {
            			    $calConfig .= '&month=' . $user->profile->calendar_month; // This is to set the week view to remember
            			}
            			if ($user->profile->calendar_day) {
            			    $calConfig .= '&day=' . $user->profile->calendar_day; // This is to set the week view to remember
            			}
            			
            	    } else {
            	        // Get link from the session
                        $calSession =  new Zend_Session_Namespace('calendarLinkSession');
            		    $calConfig = $calSession->type .
            		                 $calSession->category . $calSession->location;
            		                 
            			if ($calSession->year) {
            			    $calConfig .= '&year=' . $calSession->year; // This is to set the week view to remember
            			}
            			if ($calSession->month) {
            			    $calConfig .= '&month=' . $calSession->month; // This is to set the week view to remember
            			}
            			if ($calSession->day) {
            			    $calConfig .= '&day=' . $calSession->day; // This is to set the week view to remember
            			}
            	    }
    
            	    $this->view->calendarLink =  $calConfig;
        	    }
        	}
        }
	}
	
	/**
	 * Sets the view variable for previous and next links
	 * on the comment pages
	 *
	 * @param int $rsrcId
	 */
	public function showPrevNextLinks($rsrcId)
	{
	    $linksDescription = array();
		$discussionBarSession =  new Zend_Session_Namespace('discussionBarSession');
        
	    // Currently not providing links to bookmarks. Maybe another time. Yehoodi 3.2???
	    if ($discussionBarSession->bookmarks == true) {
            return;
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

		switch ($discussionBarSession->order) {
        	case 'activity':		// sort by resource/comment activity (NOT date range specific)
        	    $order = 'date_last_active';
				$options['rsrc_date'] = $this->userLastLogin;
				$linksDescription['order'] = "activity";
        		break;
        		
        	case 'date':			// sort by resource date (Topic Added)
        		$order = 'rsrc_date';
				$linksDescription['order'] = "date added";
        		break;
        		
        	case 'popular':			// sorty by resource vote up number
        		$order = 'votes';
				$linksDescription['order'] = "votes";
        		break;
        		
        	case 'views':			// sort by resource number of views
        		$order = 'views_lifetime';
				$linksDescription['order'] = "views";
        		break;
        		
        	case 'comment':			// sort by resource number of comments
        		$order = 'count_comments';
				$linksDescription['order'] = "comments";
        		break;
        		        		
        	default:				// default sort is resource date
        		$order = 'rsrc_date';
				$linksDescription['order'] = "date added";
        		$startDate = null;
        		break;
        }

        $options = array('current_id'     => $rsrcId,
                         'now_date'       => $today,
                         'order'          => $order,
                         'is_active'	  => DatabaseObject_Resource::STATUS_LIVE,
                         'distance'       => $discussionBarSession->distance,
                         'rsrc_type_id'   => $discussionBarSession->rsrcTypeId,
                         'cat_id'         => $discussionBarSession->catId
                         );

        switch ($discussionBarSession->range) {
            case 'lastvisit':
                // User last visit date
				$options['rsrc_date'] = $discussionBarSession->userLastLogin;
				$linksDescription['range'] = " from your last visit";
                break;
                
            case '7days':
                // user wants last 7 days
				$options['rsrc_date'] = $last7Days;
				$linksDescription['range'] = " from the last 7 days";
                break;
                
            case '30days':
                // user wants last 30 days
				$options['rsrc_date'] = $last30Days;
				$linksDescription['range'] = " from the last 30 days";
                break;
                
                // user wants last 90 days
            case '90days':
				$options['rsrc_date'] = $last90days;
				$linksDescription['range'] = " from the last 90 days";
                break;
                
            case 'allTime':
				$options['rsrc_date'] = $allTime;
				$linksDescription['range'] = " of all time";
				$today = $todayShort;
                break;
                
            default:
                // Default to last 30 days
				$options['rsrc_date'] = $last30Days;
				$linksDescription['range'] = " from the last 30 days";
                break;
        }

        if ($order == 'date_last_active') {
            $options['date_last_active_start'] = $options['rsrc_date'];
            $options['date_last_active_end'] = $today;
            unset($options['rsrc_date']);
            unset($options['now_date']);
        }

        $this->resourceOptions['bookmark'] = 1;
        $this->countOptions['bookmark'] = 1;
        unset($this->resourceOptions['rsrc_date']);
        unset($this->countOptions['rsrc_date']);


	    $links = DatabaseObject_Resource::getPrevNextLinks($this->db, $options);
	    
	    if (isset($links['prev'])) {
	       $previousUrl = DatabaseObject_Resource::getResourceUrl($this->db, $links['prev']);
	       $previousTitle = DatabaseObject_Resource::getResourceTitleByResourceId($this->db, array('rsrc_id' => $links['prev']));
	       $this->view->linkPrevURL = $this->getUrl(null,'comment') . $links['prev'] . '/' . $previousUrl;
	       $this->view->linkPrevTitle = $previousTitle['title'];
	    }
	    
	    if (isset($links['next'])) {
    	   $nextUrl = DatabaseObject_Resource::getResourceUrl($this->db, $links['next']);
	       $nextTitle = DatabaseObject_Resource::getResourceTitleByResourceId($this->db, array('rsrc_id' => $links['next']));
	       $this->view->linkNextURL = $this->getUrl(null,'comment') . $links['next'] . '/' . $nextUrl;
	       $this->view->linkNextTitle = $nextTitle['title'];
	    }

	    $links['resource'] = DatabaseObject_Resource::getResourceTypeNameByResourceId($this->db, $discussionBarSession->rsrcTypeId);
	    if (!$links['resource'] == 'everything') {
	    	$links['category'] = "";
	    } else {
	    	$links['category'] = "/" . DatabaseObject_Category::getCategoryTextById($this->db, $discussionBarSession->catId);
	    }
	    
	    $this->view->linkDescription = "Browsing {$links['resource']} {$links['category']} topics {$linksDescription['range']} by {$linksDescription['order']}.";
	    //$this->view->linkDescription = "Browsing Fetured topics from the last 90 days ordered by topic activity";
	    
//	    Zend_Debug::dump($discussionBarSession->order);
//	    Zend_Debug::dump($discussionBarSession->range);
//	    Zend_Debug::dump($discussionBarSession->rsrcTypeId);
//	    Zend_Debug::dump($discussionBarSession->catId);
	    
	}
}