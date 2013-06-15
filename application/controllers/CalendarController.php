<?php

/**
 * Yehoodi 3.0 CalendarController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Note on how this works:
 * 
 * The calendar is a jQuery plugin called FullCalendar from http://arshaw.com/fullcalendar/.
 * 
 * The required files for its use are in /js/fullcalendar and /js/jquery-qtip:
 * jquery.js
 * fullcalendar.js
 * gcal.js 					    (for Google Calendar feeds)
 * CalBrowseLink.class.js 	    (for linking Calendar Browsing actions with the FullCalendar)
 * jquery.qtip-1.0.0-rc3.min.js (for the event tooltip pop-ups)
 * 
 * The .css files are in /css
 * fullcalendar.css
 * fullcalendar_002.css
 * 
 * and the tooltip template is in /templates/calendar/calendar-tooltip.tpl
 * 
 * The header.tpl is calling the jQuery, fullcalendar and qtip js files when the Calendar page is displayed.
 * 
 * Basically in order to maintain persistent states with calendar navagation between the CalendarBrowse
 * links and the calender, I am loading CalBrowseLink.class.js which observes the links in the discussion and
 * creates the urls dynamically, passing the controller, calType, categoryUrl, location and dates.
 * 
 * /calendar/month/exchange/?location=anywhere&year=2010&month=2&day=13		[Month is 0 indexed]
 * 
 * This controller gets the parameters (default from the route.php or from a url string) and sends them to
 * the view. The calendar.tpl has the inline js needed to render the calendar and the CalBrowseLink class that
 * observes the links and builds the urls.
 * 
 * Some things to note:
 * - The 'month', 'week' and 'day' buttons on the calendar change the hidden input values on the page so I can
 * grab them to build the url if a link is clicked in the discussion. The current date of the calendar is also
 * set.
 * 
 * - All the CalBrowseLink.js file does is observe the links on top, stop the click event, and scrape the
 * DOM, building the urls for the next page.
 * 
 * - When the new page is loaded, the properties of the calendar (in calendar.tpl) are set based on the url.
 *
 */
class CalendarController extends CustomControllerAction
{
	
	protected $userLat = null;
	protected $userLon = null;
	
	public $action;
	public $categoryText;
	public $categoryUrl;
	public $catId;
	public $calType;
	public $location;
	public $year;
	public $month;
	public $day;
	public $latitude;
	public $longitude;
	public $address;
	public $userDistance;
	public $highlight;
	
	public $calStartParam;     // For the FullCalendar ajax param
	public $calEndParam;       // For the FullCalendar ajax param
	
	public $countOptions;
	public $resourceOptions;
	
	public function init()
	{
        parent::init();
        $request = $this->_request;
        
        // Check for a url in the user profile
        $this->checkURL();
		
        // Set up new date time
        $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
		$today = $dateTime->format("Y-m-d H:i:s");
		$todayShort = $dateTime->format("Y-m-d");
		
        // Get the page params
    	$this->action = $request->getParam('action');
    	
    	if (!$this->calType = $request->getParam('caltype')) {
    	    $this->calType = 'month';
    	}
    	$this->categoryUrl = $request->getParam('category');
    	$this->location = $request->getParam('location');
    	$this->year = $request->getParam('year');
    	$this->month = $request->getParam('month');
    	$this->day = $request->getParam('day');
    	$this->longitude = $request->getParam('lon');
    	$this->latitude = $request->getParam('lat');
    	$this->address = $request->getParam('loc');
    	$this->highlight = (int) $request->getParam('h');

    	// Cookie variables 
    	$cookieName = Zend_Registry::get('serverConfig')->locationCookie;
        $cookie = ( isset($_COOKIE[$cookieName]) ) ? json_decode($_COOKIE[$cookieName], true) : array();
    	//Zend_Debug::dump($cookie);die;

		// Set some defaults
	    $categoryArray = array(9,10,11);  // If we are on the EVERYWHERE tab, only allow these category ids

	    if (!$this->location) {
    	    $this->location = 'anywhere';
    	}
    	
    	// Valid lists of locations and categoryUrls
    	$otherLocations        = array('competitions', 
    	                               'camps-workshops',
    	                               'exchange',
    	                               'all',
    	                               'performance-special-event',
    	                               'swing-dance');
    	
    	$anywhereLocations   = array('competitions', 
    	                               'camps-workshops',
    	                               'exchange',
    	                               'all');
    	                               
    	// We can't have the default for 'Events by Location' be set to all event types
    	if ($this->location == 'other' && !in_array($this->categoryUrl, $otherLocations)) {
    	    // Redirect to the correct URL
    	    header("Location: /calendar/{$this->calType}/swing-dance/?location=other");
    	} elseif ($this->location == 'anywhere' && !in_array($this->categoryUrl, $anywhereLocations)) {
    	    // Redirect to the correct URL
    	    header("Location: /calendar/{$this->calType}/all/?location=anywhere");
    	}
    	
    	// Get the calendar params
    	if ($request->getParam('start') && $request->getParam('end')) {
	    	$this->calStartParam = date("Y-m-d", $request->getParam('start') - 604800 ); // Subtracting 7 days to catch dates that started a week from this.
	    	$this->calEndParam = date("Y-m-d", $request->getParam('end'));
    	}
    	
        // Get the numeric Category ID from the URL string and the human readable Category Text
    	$this->catId = DatabaseObject_Category::getCatIdByUrl($this->db, $this->categoryUrl);
        $this->categoryText = DatabaseObject_Category::getCategoryTextByUrl($this->db, $this->categoryUrl);

		//
		// Now check for the valid cookie if we don't have a user
		//
		
        // Set up the initial view of the calendar
        switch($request->getParam('caltype')) {
    	    case 'month':
    	        $this->calType = 'month';
    	        break;
    	        
    	    case 'week':
    	    case 'basicWeek':
    	        $this->calType = 'basicWeek';
    	        break;
    	        
    	    case 'day':
    	        $this->calType = 'basicDay';
    	        break;

    	    default:
    	        $this->calType = 'month';
    	        break;
    	}
    	
        /**
         * Here is where we check for location stuff.
         * This is ONLY when location is set to OTHER.
         * 
         * Priority for location sources is as follows:
         * 
         * 1. $lon and $lat variables from the URL string
         * 2. Any location cookie
         * 3. User's location setting
         * 
         */
    	
        $distance = null;
        
        if ($request->getParam('location') == 'other') {
		    $this->location = 'other';
		    $categoryArray = array(9,10,11,13,14); // If we are on the OTHER tab, allow all category ids
			// URL check
            if($this->longitude && $this->latitude) {

			    // We still use the users location settings if available
                if ($this->userDistance) {
			        $userDisatance = $this->userDistance;
			    } else {
                    $userDisatance = DatabaseObject_Resource::getDistance('medium', 'mi');
			    }
			    
			    $distance = DatabaseObject_Resource::mysqlHaversine($this->latitude,
																	$this->longitude,
																	$userDisatance,
																	'mi' );
			} 
			// Cookie check
			elseif($cookie['lon'] && $cookie['lat']) {

			    $userDisatance = DatabaseObject_Resource::getDistance('medium', 'mi');
				$distance = DatabaseObject_Resource::mysqlHaversine($cookie['lat'],
																	$cookie['lon'],
																	$userDisatance,
																	'mi' );
			} 
			// User location check
			elseif($this->userLon && $this->userLat) {

				$distance = DatabaseObject_Resource::mysqlHaversine($this->userLat,
																	$this->userLon,
																	$this->userDistance,
																	$this->userUnit );
			} else {
    		    $this->view->locationAsk = 'true';
    		}
		}
	
		// set the default resource options
		$this->resourceOptions = array(
            'cat_id'		=> ($this->catId) ? $this->catId : $categoryArray,	// category id
            'to'			=> '',										// date range to pass into TO
            //'rsrc_date'		=> $options['rsrc_date'],					// resource date
            'start_date'	=> $this->calStartParam,					// EVENT specific date range
            'after_date'	=> $this->calEndParam,						// EVENT specific date range
            'distance'		=> $distance,								// EVENT specific distance
            'is_active'		=> DatabaseObject_Resource::STATUS_LIVE, 	// status of the resource, active or not
            'range'         => md5($this->calStartParam . $this->calEndParam)
            // 'user_id' => '' 		// User id to filter on
        );
		
    } // init

	/**
	 * This Controller only has one action
	 *
	 */
    public function indexAction()
    {
		// Default page setup is all resources displayed
		$this->countOptions['rsrc_type_id'] = DatabaseObject_ResourceType::$event;
		$this->resourceOptions['rsrc_type_id'] = DatabaseObject_ResourceType::$event;
		$this->render();
    }

    /**
     * Renders the calendar page
     *
     */
    public function render($action = null, $name = null, $noController = false)
    {
        if ($this->_request->isXmlHttpRequest()) {

			// Event caching check
		    //$memcache = new Memcache;
		    //$memcache->connect("localhost",11211);

		    //if(!$events = $memcache->get("event:" . md5(serialize($this->resourceOptions)))) {
		    	 
			    // Cache miss: Get the events
		        $events = DatabaseObject_Resource::getEvents($this->db, $this->resourceOptions);
        		//$memcache->set("event:" . md5(serialize($this->resourceOptions)),$events, false, 300);
		    //} else {
		    	// Cache hit: pull from cache
		        //$events = $memcache->get("event:" . md5(serialize($this->resourceOptions)));
		        
		        // log cache hit
		        //$this->logCacheHit();
		    //}
	        
	        // The following line is needed to fix bad characters from killing the calendar output FogID: 651
		    mb_substitute_character("none");
		    $eventArray = array();
    		$templater = new Templater();
    		$tpl = 'calendar-tooltip.tpl';

			//Zend_Debug::dump($events);die;
    		foreach ($events as $value) {

        		$templater->title = $value->title;
        		//$templater->description = $value->descripRaw;
        		$templater->categoryName = $value->meta->categoryName;
        		$templater->fullLocation = $value->locationDescription;
	            $templater->locationCity = $value->locationCity;
	            $templater->locationState = $value->locationState;
	            $templater->locationCountry = $value->locationCountry;
	            $templater->startDate = common::neatDate($value->start_date);
	            if ($value->end_date) {
    	            $templater->endDate = common::neatDate($value->end_date);
	            }
	            $templater->numComments = $value->meta->numOfCommnets;
	            $templater->numVotes = $value->meta->voteNum;
	            $templater->numViews = $value->meta->viewsLifetime;
	            
				// Highlight an event?
				if ($this->highlight == $value->getId()) {
					$highlightState = ' highlight';
				} else {
					$highlightState = '';
				}
				
	            //Zend_Debug::dump($this->highlight);
	            $output = $templater->fetch('calendar/' . $tpl);
	            
	            $eventArray[] = array('id'          =>  $value->getId(),
	                                  'title'       =>  mb_convert_encoding($value->title, 'UTF-8'),	//FogID: 651
	                                  'start'       =>  $value->start_date,
	                                  'end'         =>  $value->end_date,
	                                  'url'         =>  '/comment/'.$value->getId().'/'.$value->resourceSeoUrlString.'/',
	                                  'className'   =>  'event-' . $value->meta->categoryUrl . $highlightState,
	                                  'description' =>  mb_convert_encoding($output, 'UTF-8')	//FogID: 651
	                                  );
	                       
	        }
	        
	

			// save the calendar category type and location
			$this->saveCalendarCategoryAndLocation();

            //Zend_Debug::Dump(json_encode($eventArray));die;
			$this->sendJson($eventArray);
            exit();
        }
    	    
        // Assign to Smarty
		$this->view->categoryUrl = $this->categoryUrl;
		$this->view->categoryText = $this->categoryText;
		$this->view->catId = $this->catId;
		
        $this->view->action = $this->action;
        $this->view->calType = $this->calType;
        $this->view->location = $this->location;
        $this->view->year = $this->year;
        if ($this->month === 0) {
            $this->view->month = '0';
        } elseif (!isset($this->month)) {
            $this->view->month = date("m") - 1;     // I have to force the date in the calendar
        } else {
            $this->view->month = $this->month;
        }
        $this->view->day = $this->day;
        $this->view->lon = $this->longitude;
        $this->view->lat = $this->latitude;
        
        $cookie = json_decode($_COOKIE[Zend_Registry::get('serverConfig')->locationCookie]);
        
        if ( $this->address ) {
            // From the url
            $this->view->address = $this->address;
        } elseif (!empty($cookie->loc)) {
            // From the cookie
            $this->view->address = utf8_decode($cookie->loc);
        } elseif ($this->identity->location) {
            // From the user's settings
            $this->view->address = $this->identity->location;
        } else {
            $this->view->address = '';
        }

	    // For highlighting a single event
        $this->view->highlight = $this->highlight;
        
        // send messages to the user
    	$this->view->messages = $this->_helper->_flashMessenger->getMessages();

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Calendar of Swing and Lindy Hop Events', $this->getUrl( null, 'calendar'));

        // Render!
        $this->_helper->viewRenderer('index');
    }

    /**
     * Saves the calendar url to the users
     * profile so the next time they hit Calendar
     * it starts where it left off.
     * 
     * This does NOT include the first parameter which
     * is handled by savecalendartypeAction() in the
     * CalendarajaxController
     *
     */
	protected function saveCalendarCategoryAndLocation()
    {
    	// save the calendar url so the user comes back to this page
		$url = "{$this->categoryUrl}/";
		if ($this->location) {
		    $url .= "?location={$this->location}";
		}
		
		if ($this->longitude) {
		    $url .= "&lon={$this->longitude}";
		}
		
		if ($this->latitude) {
		    $url .= "&lat={$this->latitude}";
		}
		
		if ($this->address) {
		    $url .= "&loc={$this->address}";
		}
		
		if ($this->identity = Zend_Auth::getInstance()->getIdentity()) {
			$user = new DatabaseObject_User($this->db);
			$user->load($this->identity->user_id);
			
			$user->profile->calendar_cat_loc = $url;
			$user->save();
		} else {
		    // Save it to the session
		    $calSession =  new Zend_Session_Namespace('calendarLinkSession');
		    $calSession->category = "{$this->categoryUrl}/";
		    //$calSession->type = $this->calType;
		    if ($this->location) {
		      $calSession->location = "?location={$this->location}";
		    }
    		
		    if ($this->longitude) {
    		    $calSession->longitude = "&lon={$this->longitude}";
    		}

    		if ($this->latitude) {
    		    $calSession->latitude = "&lat={$this->latitude}";
    		}
		}
    }
 
    /**
     * Handles checking the current calendars
     * last url for returning to the page where
     * the user left off.
     *
     */
    protected function checkURL()
    {
		$calConfig = null;
        
        // get our user (if logged in)
        if ($this->identity = Zend_Auth::getInstance()->getIdentity()) {
			$user = new DatabaseObject_User($this->db);
			$user->load($this->identity->user_id);
			
			// get user's location info
			$this->userLat = $user->profile->latitude;
			$this->userLon = $user->profile->longitude;
			$this->userUnit = $user->profile->unit;
			$this->userDistance = DatabaseObject_Resource::getDistance($user->profile->distance, $this->userUnit);
			$this->userLastLogin = $user->date_last_updated;

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
		    // Get it from the session
            $calSession =  new Zend_Session_Namespace('calendarLinkSession');
		    $calConfig = $calSession->type . '/' .
		                 $calSession->category . $calSession->location;
		                 
			// get user's location info
			$this->userLat = $calSession->latitude;
			$this->userLon = $calSession->longitude;
			$this->userUnit = 'mi';
			$this->userDistance = DatabaseObject_Resource::getDistance('medium','mi');

			if ($calSession->year) {
			    $calConfig .= '&year=' . $calSession->year; // This is to set the week view to remember
			}
			if ($calSession->month) {
			    $calConfig .= '&month=' . $calSession->month; // This is to set the week view to remember
			}
			if ($calSession->day) {
			    $calConfig .= '&day=' . $calSession->day; // This is to set the week view to remember
			}
		    
		    
		    //Zend_Debug::dump($calConfig);
		}

		// Redirect
        if ($this->getRequest()->getRequestUri() == "/calendar/") {
			// check for discussion bar settings
			if($calConfig) {
	        	$this->_redirect('/calendar/' . $calConfig);
	        	exit;
			}
        }
    }
}