<?php
    /**
     * handles the resource submissions
     *
     */
	class FormProcessor_Resource extends FormProcessor
    {
        // This is our whitelist of allowed html tags for resources
        static $tags =     '<a>
                            <b>
                            <blockquote>
                            <code>
                            <del>
                            <dd>
                            <dl>
                            <dt>
                            <em>
                            <h1>
                            <h2>
                            <h3>
                            <i>
                            <img>
                            <kbd>
                            <li>
                            <ol>
                            <p>
                            <pre>
                            <s>
                            <sub>
                            <sup>
                            <strike>
                            <strong>
                            <u>
                            <ul>
                            <br>
                            <hr>';

        protected $db = null;
        public $resource = null;
        public $user = null;
        protected $_validateOnly = false;
        
        public $image;
        public $rsrc_image_id;
        //public $location;

        // Constants for switching the required fileds
        const CAT_COMPETITION =		9;
        const CAT_CAMP_WKSHP =		10;
        const CAT_EXCHANGE =		11;
        const CAT_RECUR_DANCE =		13;
        const CAT_PERF_SPECIAL =	14;
        
        // Featured categories
        const FEATURED_NEWS = 		1;
        const FEATURED_PODCAST = 	2;
        const FEATURED_RADIO = 		3;
        const FEATURED_COB = 		4;
        
        /**
         * Constructor for the Resource Object
         *
         * @param database object $db
         * @param user id int $user_id
         * @param resource id int $rsrcId
         * @param request object $request
         */
        public function __construct($db, $user_id, $rsrcId = 0, $request)
        {
            parent::__construct();
            
            // Database object
            $this->db = $db;

            // Instantiate a user object
            $this->user = new DatabaseObject_User($this->db);
            $this->user->load($user_id);
            
            // Instantiate new resource object
            $this->resource = new DatabaseObject_Resource($this->db);
			$this->resource->loadForUser(Zend_Auth::getInstance()->getIdentity(), $rsrcId);
			
            // Instantiate new resource_image object
            $this->image = new DatabaseObject_ResourceImage($this->db);
			$image_id = DatabaseObject_ResourceImage::loadImageId($this->db, $rsrcId);
			
            $this->image->load($image_id);
            
            // If user is a mod, instantiate an Extended Resource obj
            if(Zend_Auth::getInstance()->getIdentity()->mod) {
            	$this->extended = new Extended_Resource($this->db);
            	$this->extended->setResourceId($rsrcId);
            	$this->extended->load();
            }
            
            // This check happens because of the three submit buttons on the submit page
            // I have to check these weren't clicked.
            if ($this->resource->isSaved() 
            	&& $request->getPost('prevImage') != "Upload" 
            	&& $request->getPost('deleteImage') != "Remove Image") {
				
				// The resource exists so load in its values
				$this->cat_id= $this->resource->cat_id;
				$this->last_comment_id= $this->resource->last_comment_id;
				
				$this->title = $this->resource->title;
				
				// getting the raw unparsed version of the description direct from the resource object
				$this->descrip = $this->resource->descrip;

				$this->url = $this->resource->url;
				$this->start_date = $this->resource->start_date;
				$this->end_date = $this->resource->end_date;
				$this->repetition = $this->resource->repetition;
				$this->duration = $this->resource->duration;
				$this->rsrc_date = $this->resource->rsrc_date;
				$this->caption = $this->image->caption;
				
				// For the image
				if ($this->resource->filename) {
				    $this->filename = $this->resource->filename;
				}
				
				// Extended stuff if needed
				if(is_object($this->extended)) {
					$this->show_name = $this->extended->show_name;
					$this->show_code = $this->extended->show_code;
					$this->show_episode = $this->extended->show_episode;
					$this->internal_page_url = $this->extended->internal_page_url;
					$this->internal_page_link_text = $this->extended->internal_page_link_text;
					$this->media_url = $this->extended->media_url;
					$this->flash_url = $this->extended->flash_url;
					$this->shownotes = $this->extended->shownotes;
					$this->artist = $this->extended->artist;
					$this->album = $this->extended->album;
					$this->extraFramInfo = $this->extended->extraFramInfo;
				}
			} else {
				$this->resource->user_id = $this->user->getId();

				// The resource is new so get values from the form
				$this->cat_id= $request->getPost('cat_id');
				//$this->last_comment_id= $request->getPost('last_comment_id');
				
				$this->title = $request->getPost('title');
				$this->descrip = $request->getPost('inputDescription');
				$this->url = $request->getPost('url_text');
				$this->caption = $request->getPost('caption');

				// Brand new form? Dates posted to the form yet?
				if($request->isPost()) {
					$startDateYear = $request->getPost('startDateYear');
		            $startDateMonth = $request->getPost('startDateMonth');
		            $startDateDay = $request->getPost('startDateDay');
		            $this->start_date = "{$startDateYear}-{$startDateMonth}-{$startDateDay} 00:00:00";
	
		            $endDateYear = $request->getPost('endDateYear');
		            $endDateMonth = $request->getPost('endDateMonth');
		            $endDateDay = $request->getPost('endDateDay');
		            $this->end_date = "{$endDateYear}-{$endDateMonth}-{$endDateDay} 00:00:00";
		            
		            // post date
		            $rsrcDateMonth = $request->getPost('rsrcDateMonth');
		            $rsrcDateDay = $request->getPost('rsrcDateDay');
		            $rsrcDateYear = $request->getPost('rsrcDateYear');
		            $rsrcTimeHour = $request->getPost('rsrcTimeHour');
		            $rsrcTimeMinute = $request->getPost('rsrcTimeMinute');
    	            $this->rsrc_date = "{$rsrcDateYear}-{$rsrcDateMonth}-{$rsrcDateDay} {$rsrcTimeHour}:{$rsrcTimeMinute}:00";
				}
				
	            $this->repetition = $request->getPost('repetition');
				$this->duration = $request->getPost('duration');
				
				// Once again, check for moderator based Extended Resource data
				if(is_object($this->extended)) {
					$this->show_name = $request->getPost('show_name');
					$this->show_code = $request->getPost('show_code');
					$this->show_episode = $request->getPost('show_episode');
					$this->internal_page_url = $request->getPost('internal_page_url');
					$this->internal_page_link_text = $request->getPost('internal_page_link_text');
					$this->media_url = $request->getPost('media_url');
					$this->flash_url = $request->getPost('flash_url');
					$this->shownotes = $request->getPost('shownotes');
					$this->artist = $request->getPost('artist');
					$this->album = $request->getPost('album');
					$this->extraFramInfo = $request->getPost('extraFramInfo');
				}
				
			}
            
        }

        public function validateOnly($flag)
        {
            $this->_validateOnly = (bool) $flag;
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
			// post flood checking
		    $memcache = new Memcache;
		    $memcache->connect("127.0.0.1",11211); # You might need to set "localhost" to "127.0.0.1"

		    if($memcache->get("key") == 'r_'.$this->resource->user_id) {
				$this->addError('resource', 'You must wait at least one minute before posting another topic.');
		    }
			
		    // Set up new date time
	        $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
			$fullDate = $dateTime->format("Y-m-d H:i:s");
			$shortDate = $dateTime->format("Y-m-d");

            /**
             * FORM VALIDATION
             * 
             * Standard validation for ALL resource
             * cases:
             */
            
            // validate resource type
            $this->rsrc_type_id = $request->getPost('rsrc_type_id');
            // Kinda hackish, but it's a valid check...
            $types = Zend_Registry::get('resourceConfig');
            
            if ($this->rsrc_type_id != $types->featured &&
            	$this->rsrc_type_id != $types->lindy &&
            	$this->rsrc_type_id != $types->event &&
            	$this->rsrc_type_id != $types->lounge &&
            	$this->rsrc_type_id != $types->biz &&
            	$this->rsrc_type_id != $types->admin )
            	$this->addError('rsrc_type_id','Invalid resource type specified');
            	
            // validate if a regular user is somehow posting to a moderator area
            $modOnly = array($types->featured,
                             $types->admin
                             );
            if (!Zend_Auth::getInstance()->getIdentity()->mod && in_array($this->rsrc_type_id, $modOnly)) {
            	$this->addError('title', 'Your do not have permission to post in this category. Please choose another one.');
            }
            
            // validate category
            $this->cat_id = $request->getPost('cat_id');
            if ($this->cat_id <= 0)
            	$this->addError('cat_id','Please choose a category for your resource');
            	
            // validate title
            $this->title = $this->sanitize(trim($request->getPost('title')));
            if (strlen($this->title) == 0)
                $this->addError('title', 'Please enter a title');
                
            if (strlen($this->title) > 60)
            	$this->addError('title', 'Your title is too long. Please shorten it.');

            // validate url
            $this->url = $request->getPost('url_text');
            if (strlen($this->url) > 0) {
	            if (stripos($this->url, 'http://') === false && stripos($this->url, 'https://') === false ) {
	            	$this->url = 'http://' . $this->url;
	            }
	            $validator = new Zend_Validate_Regex("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i");
	            if ($validator->isValid($this->url)) {
	            	// url is valid
	            } else {
	            	$this->addError('url_text', 'Please enter a valid url');
	            }
            }
            
            // Validate the notify checkbox
            $this->notify_by_email = $request->getPost('notify_by_email');
            if ($this->notify_by_email > 1) {
	            $this->addError('notify_by_email', 'An error has occured');
            }

            // validate description
            // I am indenting this because it can get a bit hairy
            	// Grab the user input
				$this->descrip = $request->getPost('inputDescription');
	            if (strlen($this->descrip) <= 0) {
	                $this->addError('inputDescription', 'Please enter a message.');
	            } elseif (strlen($this->descrip) > 65535) {
	                $this->addError('inputDescription', 'Your descripton is too long. Shorten it please.');
	            } else {
	            
		        	// Should BBCoding and stripping occur AFTER validation is done? Move this later in the code?
	            	
	            	// Strip out all the un-allowed html tags for the first resource    
		            $this->descrip = $this->cleanHtml($this->descrip, self::$tags);
	            }
                
            // Validate the image caption
	        $this->caption = $this->sanitize(trim($request->getPost('caption')));
            if (strlen($this->caption) > 255) {
            	$this->addError('caption', 'Your caption is too long. Please shorten it.');
            }
			
        	/**
             * The following validation is specific
             * to a particular resource
             */
            switch ($this->cat_id)
            {
            	// If the category is any of the event types, dates and location are manditory
            	case self::CAT_COMPETITION:		// Event Resource
            	case self::CAT_EXCHANGE: 		// Event Resource
            	case self::CAT_CAMP_WKSHP:		// Event Resource
            	case self::CAT_RECUR_DANCE:		// Event Resource
            	case self::CAT_PERF_SPECIAL:	// Event Resource
		            // validate frequency -- DISABLED
		            //$this->repetition = $request->getPost('repetition');
		            $this->repetition = 'none';
		            if (strlen($this->repetition) == 0 )
		                $this->addError('repetition', 'Invalid event frequency specified');
		                
		            // validate duration
		            $this->duration = $request->getPost('duration');
		            if ($this->duration <= 0 )
		                $this->addError('duration', 'Invalid event duration specified');
            	
		            // validate start_date
		            $startDateYear = $request->getPost('startDateYear');
		            $startDateMonth = $request->getPost('startDateMonth');
		            $startDateDay = $request->getPost('startDateDay');
		            
		            // validate end_date
		            $endDateYear = $request->getPost('endDateYear');
		            $endDateMonth = $request->getPost('endDateMonth');
		            $endDateDay = $request->getPost('endDateDay');
			            
		            // Check validity of start date first
		            $this->start_date = "{$startDateYear}-{$startDateMonth}-{$startDateDay}";
		            if (checkdate($startDateMonth, $startDateDay, $startDateYear) 
		            		&& $startDateYear . '-' . $startDateMonth . '-' . $startDateDay >= $shortDate 
		            		|| Zend_Auth::getInstance()->getIdentity()->mod ) {
            	
			            // Start date is good. Now check end date.
		            	$this->end_date = "{$endDateYear}-{$endDateMonth}-{$endDateDay}";
			            if (checkdate($endDateMonth, $endDateDay, $endDateYear)
		            		&& $endDateYear . '-' . $endDateMonth . '-' . $endDateDay >= $shortDate
		            		|| Zend_Auth::getInstance()->getIdentity()->mod ) {
	            	
				            // end date is also good. Now look at repetition and create dates
			            	

			            	// If the repetition is 'none' then the end date is the start date plus the duration:
				            if ($this->repetition == 'none') {
				            	
				            	// Create a new date object (the cool extended date class)
				            	$newEndDate = new Pos_Date();
				            	$newEndDate->setFromMySQL($this->start_date);

				            	if ($this->duration > 1 ) {
					            	$newEndDate->addDays( $this->duration - 1 );
				            	}
				            	$this->end_date = $newEndDate->format("Y-m-d");
					            
			            	// If the repetition is 'weekly' we have to make sure there is at least a week between start and end dates
				            } elseif ($this->repetition == 'weekly') {

				            	// Create a new date object (the cool extended date class)
				            	$newMinDate = new Pos_Date();
				            	$newMinDate->setFromMySQL($this->start_date);

				            	$newMinDate->addWeeks(1);
				            	$endDateWeeklyMin = $newMinDate->format("Y-m-d");
				            	
				            	if ($this->end_date < $endDateWeeklyMin) {
				            		$this->addError('startDateMonth', 'Weekly events must be at least a week long.');
				            	}			            

				            // If the repetition is 'monthly' we have to make sure there is at least a month between start and end dates
				            } elseif ($this->repetition == 'monthly') {

				            	// Create a new date object (the cool extended date class)
				            	$newMinDate = new Pos_Date();
				            	$newMinDate->setFromMySQL($this->start_date);

				            	$newMinDate->addMonths(1);
				            	$endDateMonthlyMin = $newMinDate->format("Y-m-d");
				            	
				            	if ($this->end_date < $endDateMonthlyMin) {
				            		$this->addError('startDateMonth', 'Monthly events must be at least a month long.');
				            	}			            
				            }
			            } else {
			                $this->addError('endDateMonth', 'Invalid event ending date specified');
			            }
		            } else {
			            $this->addError('startDateMonth', 'Invalid event starting date specified');
		            }
            	
		            // validate location
		            // for now I am checking against the location mapSession or the table to see if a primary location has been set
		            // for this event
		            
	            	// Check the session for locations
		            $map = new Zend_Session_Namespace('mapSession');
		            if(!$map->location) {
	             		$this->addError('location', 'Please enter a valid location for the event');
		            }
		            
            		// validate Extra Fram info
		            $this->extraFramInfo = $request->getPost('extraFramInfo');
		            if (strlen($this->extraFramInfo) > 65535 )
		                $this->addError('extraFramInfo', 'Fram Info too long.');
            		
					break;
				
            	case self::FEATURED_COB:		// Featured Category	
            	case self::FEATURED_NEWS:		// Featured Category	
            	case self::FEATURED_PODCAST:	// Featured Category	
            	case self::FEATURED_RADIO:		// Featured Category
            		
            		// validate show_name
		            $this->show_name = $request->getPost('show_name');
		            if (strlen($this->show_name) > 128 )
		                $this->addError('show_name', 'Show name is too long.');
            		
            		// validate show_code
		            $this->show_code = $request->getPost('show_code');
		            if (strlen($this->show_code) > 4 )
		                $this->addError('show_code', 'Invalid Show Code.');
            		
            		// validate show_episode
		            $this->show_episode = $request->getPost('show_episode');
		            if (strlen($this->show_episode) > 5 )
		                $this->addError('show_episode', 'Invalid Show Episode.');
            		
            		// validate internal_page_url
		            $this->internal_page_url = $request->getPost('internal_page_url');
		            if (strlen($this->internal_page_url) > 256 )
		                $this->addError('internal_page_url', 'URL is too long.');
            		
            		// validate internal_page_url
		            $this->internal_page_link_text = $request->getPost('internal_page_link_text');
		            if (strlen($this->internal_page_link_text) > 256 )
		                $this->addError('internal_page_link_text', 'URL is too long.');
            		
            		// validate media_url
		            $this->media_url = $request->getPost('media_url');
		            if (strlen($this->media_url) > 256 )
		                $this->addError('media_url', 'URL is too long.');
            		
            		// validate flash_url
		            $this->flash_url = $request->getPost('flash_url');
		            if (strlen($this->flash_url) > 256 )
		                $this->addError('flash_url', 'URL is too long.');
            		
            		// validate shownotes
		            $this->shownotes = $request->getPost('shownotes');
		            if (strlen($this->shownotes) > 65535 )
		                $this->addError('shownotes', 'Show notes too long.');
            		
            		// validate artist
		            $this->artist = $request->getPost('artist');
		            if (strlen($this->artist) > 50 )
		                $this->addError('artist', 'Artist Name too long.');
            		
            		// validate album
		            $this->album = $request->getPost('album');
		            if (strlen($this->album) > 128 )
		                $this->addError('album', 'Album Name too long.');
            		
		            break;

					
				default: // Any others don't require a location
            		// Clear these fields
					$this->repetition = 'none';
					$this->duration = 0 ;
            		break;
            }
            
			$this->resource->rsrc_type_id = $this->rsrc_type_id;
			$this->resource->cat_id = $this->cat_id;

			// Finally, the mods can alter the rsrcDate if they like
			if(Zend_Auth::getInstance()->getIdentity()->mod) {
	            // validate start_date
	            $rsrcDateYear = $request->getPost('rsrcDateYear');
	            $rsrcDateMonth = $request->getPost('rsrcDateMonth');
	            $rsrcDateDay = $request->getPost('rsrcDateDay');
	            $rsrcTimeHour = $request->getPost('rsrcTimeHour');
	            $rsrcTimeMinute = $request->getPost('rsrcTimeMinute');

	            // validate the date
	            if (!checkdate($rsrcDateMonth, $rsrcDateDay, $rsrcDateYear)) {
	                $this->addError('rsrcDate', 'That is an invalid date.');
	            }
				
	            $this->rsrc_date = "{$rsrcDateYear}-{$rsrcDateMonth}-{$rsrcDateDay} {$rsrcTimeHour}:{$rsrcTimeMinute}:00";
			}
            
			// if no errors have occurred, save the resource
			if (!$this->_validateOnly && !$this->hasError()) {
			    $this->resource->rsrc_type_id = $this->rsrc_type_id;
				$this->resource->cat_id = $this->cat_id;
				$this->resource->title = $this->title;
				$this->resource->descrip = $this->descrip;
				$this->resource->url = $this->url;
				$this->resource->start_date = $this->start_date;
				$this->resource->end_date = $this->end_date;
				$this->resource->repetition = $this->repetition;
				$this->resource->duration = $this->duration;
				$this->resource->remote_ip = ip2long($_SERVER['REMOTE_ADDR']);

    			// Add the caption to the imagePreview session
    			if ($this->caption) {
        			$imagePreview = new Zend_Session_Namespace('submitPreview');
    				$imagePreview->caption = $this->caption;
    			}

				// Kill the url in the resource_url db
                //DatabaseObject_ResourceUrl::removeUniqueUrl($this->db, $this->resource->getId());
    
				// If the user is a mod we can set the rsrc date
				if(Zend_Auth::getInstance()->getIdentity()->mod) {
					$this->resource->rsrc_date = $this->rsrc_date;
					$this->resource->date_last_active = $this->rsrc_date;
				}
				
				// update the last edited date if this is resource is live and saved (it's in the wild)
				// ADDED don't change the date edited if the user is a mod
        		if($this->resource->isLive() && $this->resource->isSaved() && !Zend_Auth::getInstance()->getIdentity()->mod) {
        			$this->resource->date_edited = $fullDate;
        		}
				
        		// send it live if it's Create & Post otherwise set it to Draft
				if($request->getPost('Submit') == "Save Draft") {
	        		$this->resource->sendBackToDraft();
				} else {
					// Moderator check for active/inactive setting
					if(Zend_Auth::getInstance()->getIdentity()->mod) {
						$this->is_active = $request->getPost('is_active');
						($this->is_active == 1) ? $this->resource->sendLive() : $this->resource->sendInactive();
					} else {
						$this->resource->sendLive();
					}
        		
	        		
	        		// When normal members take a story live, the rsrc_date must be set to the current date
	        		// for the story to come up as new on the page
						//I don't want this behavior
//	        		if(!Zend_Auth::getInstance()->getIdentity()->mod) {
//	        		    $this->resource->rsrc_date = $fullDate;
//	        		}
				}

				// Sets the increase post_count flag for later. 
				// I have to do this because it has to ahppen BEFORE the resource is actually saved.
				if ($this->resource->isSaved()) {
				    $increasePostCount = false;
				} else {
				    $increasePostCount = true;
				}
				
				// Finally, let's save this sucker
				if($this->resource->save()) {

					// Extended Resource info
					if(is_object($this->extended)) {
			        	$this->extended->setResourceId($this->resource->getId());
			        	$this->extended->load();
			        	if($this->show_name) {
							$this->extended->show_name = $this->show_name;
			        	}
			        	if($this->show_code) {
							$this->extended->show_code = $this->show_code;
			        	}
			        	if($this->show_episode) {
							$this->extended->show_episode = $this->show_episode;
			        	}
			        	if($this->internal_page_url) {
							$this->extended->internal_page_url = $this->internal_page_url;
			        	}
			        	if($this->internal_page_link_text) {
							$this->extended->internal_page_link_text = $this->internal_page_link_text;
			        	}
			        	if($this->media_url) {
							$this->extended->media_url = $this->media_url;
			        	}
			        	if($this->flash_url) {
							$this->extended->flash_url = $this->flash_url;
			        	}
			        	if($this->shownotes) {
							$this->extended->shownotes = $this->shownotes;
			        	}
			        	if($this->artist) {
							$this->extended->artist = $this->artist;
			        	}
			        	if($this->album) {
							$this->extended->album = $this->album;
			        	}
			        	if($this->extraFramInfo) {
							$this->extended->extraFramInfo = $this->extraFramInfo;
			        	}
			            $this->extended->save();
					}
					
					$locations = new Zend_Session_Namespace('mapSession');
					if ($locations->location) {
						// Remove the current locations from the db
						$map = new DatabaseObject_Location($this->db);
						$map->deleteLocations($this->db, array('rsrc_id' => $this->resource->getId()));
						
						foreach ($locations as $values) {
							foreach ($values as $v) {
								$location = new DatabaseObject_Location($this->db);
								$location->rsrc_id 			= $this->resource->getId();
								$location->longitude 		= $v['longitude'];
								$location->latitude 		= $v['latitude'];
								$location->description 		= $v['description'];
								$location->street_address 	= $v['street_address'];
								$location->city 			= $v['city'];
								$location->state 			= $v['state'];
								$location->zip 				= $v['zip'];
								$location->country 			= $v['country'];
								$location->primary_location = $v['primary_location'];
		
								$location->save();
							}
						}
					}
					
					// post flood memcaching isn't for MODS or DRAFT messages.
					if (!Zend_Auth::getInstance()->getIdentity()->mod && !$this->resource->is_active == DatabaseObject_Resource::STATUS_DRAFT ) {
					    $memcache->set("key",'r_'.$this->resource->user_id, false, 60);
					}

					// If my post count increase flag is set above, then increase. Otherwise it's probably an edit.
					if ($increasePostCount) {
    				    DatabaseObject_User::addPostCount($this->db, $this->resource->user_id);
					}
					
					// Check for user notifications
					$this->processNotificationStatus($this->notify_by_email);
					
					// Clear the Resource cache keys
					DatabaseObject_Resource::clearResourceCache();
				}
			}

            // return true if no errors have occurred
            return !$this->hasError();
        }
        
		/**
		 * Updates the user_resource_notify table
		 * for this user and resource
		 *
		 */
        protected function processNotificationStatus($notifyStatus)
		{
			// if the story doesn't belong to me, don't change the notify status
			if($this->resource->user_id != Zend_Auth::getInstance()->getIdentity()->user_id) {
				return;
			}
			
			$notify = new DatabaseObject_UserResourceNotify($this->db);
	
			if ($notifyStatus == 'true') {
	
				// Is this user already being notified? Update the entry
				// This usually happens when the user changes notify status
				// from the resource EDIT screen
				if ($notifyId = DatabaseObject_UserResourceNotify::getNotify($this->db, array('rsrc_id' => $this->resource->getId(),
																									'user_id' => $this->resource->user_id))) {
					$notify->load($notifyId);
					$notify->notify_status = DatabaseObject_UserResourceNotify::NOTIFIED_NO  ;
					$notify->save();
				} else {
					// create a new entry in the user_resource_notify table
					$notify->user_id = $this->resource->user_id;
					$notify->rsrc_id = $this->resource->getId();
					$notify->notify_status = DatabaseObject_UserResourceNotify::NOTIFIED_NO  ;
					$notify->save();
				}
			} else {
				// turn notification off (delete the entry in the table)
				if ($notifyId = DatabaseObject_UserResourceNotify::getNotify($this->db, array('rsrc_id' => $this->resource->getId(),
																									'user_id' => $this->resource->user_id))) {
					$notify->load($notifyId);
					$notify->delete();
				}
			}
		}
    }
?>