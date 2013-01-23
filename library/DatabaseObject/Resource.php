<?php
    class DatabaseObject_Resource extends DatabaseObject
    {

        const STATUS_INACTIVE = 0;
        const STATUS_DRAFT = 2;
        const STATUS_LIVE = 1;

        const RSRC_CACHE_KEY_TIME = 43200; // 12 hours
	        
        //public $images = array();
        public $meta = null;
        public $userMeta = null;
        public $neatStartDate = null;
        public $neatEndDate = null;
        public $image;
        public $resourceSeoUrl;
        public $resourceSeoUrlString;
        public $extended = null;
        public $editLink;
        public $modLink;
        public $locationsArray;
        public $locationDescription;
        public $locationCity;
        public $locationState;
        public $locationCountry;
        public $descripRaw = null;

        public $calendarLink = null;
        public $bookmark = null;
        public $vote = null;
        public $calendar = null;
        public $notify = null;
        public $report = null;
        public $avatar;
        
        protected $authUserId = null;
        protected $authUserType = null;
        protected $dateLastUpdated = null;
        protected $dateTime;
        protected $filterDirty = '';
        
    	/**
    	 * Constructor:
    	 * 
    	 * Defines fields in the db
    	 * Instantiates a DateTime class
    	 * Instantiates a ResourceUrl class for SEO
    	 * Instantiates a ResourceImage class for the image and thumb
    	 *
    	 * 
    	 * @param unknown_type $db
    	 */
        public function __construct($db)
        {
            parent::__construct($db, 'resource', 'rsrc_id');

        	// Get current user_id if any
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {
        		$this->authUserId = $auth->getIdentity()->user_id;
        		$this->authUserType = $auth->getIdentity()->user_type;
        		$this->dateLastUpdated = $auth->getIdentity()->date_last_updated;
        	}
            // Set up new date time
            $this->dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
            
            // These are required
            $this->add('user_id');
            $this->add('cat_id',0);
            $this->add('last_comment_id',0);
            $this->add('title','');
            $this->add('descrip','');
            $this->add('rsrc_date', $this->dateTime->format("Y-m-d H:i:s"));
            $this->add('date_edited','0000-00-00 00:00:00');
			$this->add('url','');
            $this->add('start_date',"0000-00-00 00:00:00");
            $this->add('end_date',"0000-00-00 00:00:00");
            $this->add('repetition',"none");
            $this->add('duration',0);
			$this->add('votes',0);
            $this->add('closed',0);
            $this->add('sticky',0);
            $this->add('date_last_active', $this->dateTime->format("Y-m-d H:i:s"));
            $this->add('count_comments',0);
            $this->add('is_active', self::STATUS_DRAFT);
            $this->add('remote_ip',"");
            
        	// Instantiate a new Resource URL object
            $this->resourceSeoUrl = new DatabaseObject_ResourceUrl($this->_db);
            
            // Instantiate a new Resource image object?
            $this->image = new DatabaseObject_ResourceImage($this->_db);
            
            // Instantiate a new Resource extended object
            $this->extended = new Extended_Resource($this->_db);
        }

        protected function preInsert()
        {
        	return true;
        }

        protected function postLoad()
        {
        	// Load the extended info?
        	if(is_object($this->extended)) {
	        	$this->extended->setResourceId($this->getId());
	            $this->extended->load();
        	}
        	
            // resource level updates
        	$this->neatStartDate = ($this->start_date == "0000-00-00 00:00:00" ) ? "" : common::neatDate($this->start_date);
        	$this->neatEndDate = ($this->end_date == "0000-00-00 00:00:00" || $this->start_date >= $this->end_date ) ? "" : common::neatDate($this->end_date);

        	// instantiate the meta object
        	$this->meta = new DatabaseObject_ResourceMeta($this->_db, $this);
        	
        	// set the formatting of the date and add to the meta
        	$this->meta->neatPostedDate = common::neatDate($this->rsrc_date);
        	
        	// Added to decode all title html entities for the template
        	$this->title = html_entity_decode($this->title);
        	
        	// 2. Added to decode all description html entities for the template
        	$this->descrip = html_entity_decode($this->descrip);
        	
        	// Gets the SEO url for this resource
        	$this->resourceSeoUrlString = $this->getResourceUrl($this->_db, $this->_id);
        	
            // Instantiate new resource_image object
            $this->image = new DatabaseObject_ResourceImage($this->_db);
			$image_id = DatabaseObject_ResourceImage::loadImageId($this->_db, $this->_id);
			
            $this->image->load($image_id);
            
            // get the locations of any event resources
            $options = array(
            		'rsrc_id'	=>	$this->_id 
            		);
            $this->locationsArray = DatabaseObject_Location::getLocations($this->_db, $options); //this is an array
            if (isset($this->locationsArray[0]['city'])) {
            	$this->locationCity = $this->locationsArray[0]['city'];
            }
            if (isset($this->locationsArray[0]['state'])) {
            	$this->locationState = $this->locationsArray[0]['state'];
            }
            if (isset($this->locationsArray[0]['country'])) {
            	$this->locationCountry = $this->locationsArray[0]['country'];
            }
            if (isset($this->locationsArray[0]['description'])) {
            	$this->locationDescription = $this->locationsArray[0]['description'];
            }
				
        	// Number of votes for this story
            $options = array(
            		'rsrc_id'	=>	$this->_id 
            		);
        	$this->meta->voteNum = DatabaseObject_UserVote::getVoteCountByResourceId($this->_db, $options);

        	// Not a user so default is no new comments
        	$this->meta->newComments = false;

            // Get the user meta info
            $this->userMeta = new DatabaseObject_UserMeta($this->_db, $this->user_id);

            /**
             * User Stuff!
             */
            // Get the user's bookmark if any
			if ($this->authUserId > 0 ) {
				// Is this resource owned by the current user or an ADMIN?

				if ($this->authUserId == $this->user_id ) {
					$this->editLink = "/submit/" . $this->_id;
				}

				// user has this bookmarked?
				$options = array(
							'user_id'	=>	$this->authUserId,
							'rsrc_id'	=>	$this->getId()
							);
	        	$this->bookmark = DatabaseObject_UserBookmark::getBookmark($this->_db, $options);

				// user voted on this?
	        	$this->vote = DatabaseObject_UserVote::getVote($this->_db, $options);

				// Jump to calendar link (for active events only)
				//$this->calendarLink = "month/swing-dance/?location=other&year=2010&month=3&day=24&lon=-78.7483629&lat=42.4331178&loc=Brooklyn,%20NY,%20USA&h=171511";
					
				switch ($this->meta->categoryUrl) {
					case 'competitions':
					case 'camps-workshops':
					case 'exchange':
						$this->calendarLink = 'month/';
						$this->calendarLink .= $this->meta->categoryUrl . '/?location=anywhere';
						$this->calendarLink .= '&year=' . substr($this->start_date,0,4);
						
						$month = substr($this->start_date,5,2) - 1;
						if ($month === 0) {
							$this->calendarLink .= '&month=0';
						} else {
							$this->calendarLink .= '&month=' . $month;
						}
						$this->calendarLink .= '&day=' . substr($this->start_date,8,2);
						
						$this->calendarLink .= '&h=' . $this->getId();
						break;
						
					case 'swing-dance':
					case 'performance-special-event':
						$this->calendarLink = 'month/';
						$this->calendarLink .= $this->meta->categoryUrl . '/?location=other';
						$this->calendarLink .= '&lon=' . $this->locationsArray[0]['longitude'];
						$this->calendarLink .= '&lat=' . $this->locationsArray[0]['latitude'];
						$this->calendarLink .= '&loc=' . $this->locationsArray[0]['description'];
						$this->calendarLink .= '&year=' . substr($this->start_date,0,4);
						
						$month = substr($this->start_date,5,2) - 1;
						if ($month === 0) {
							$this->calendarLink .= '&month=0';
						} else {
							$this->calendarLink .= '&month=' . $month;
						}
						$this->calendarLink .= '&day=' . substr($this->start_date,8,2);
						
						$this->calendarLink .= '&h=' . $this->getId();
						break;
				}
	        	
	        	// user added this to her calendar?
	        	//$this->calendar = DatabaseObject_UserCalendar::getCalendar($this->_db, $options);

				// user is watching this resource?
				// this pulls back an ID but we don't use it, we just need to see if there is one.
	        	$this->notify = DatabaseObject_UserResourceNotify::getNotify($this->_db, $options);

				// user has reported this to the mods?
	        	$this->report = DatabaseObject_ResourceReport::getReport($this->_db, $options);

	        	// Dirty word filter on?
	        	$this->filterDirty = DatabaseObject_User::checkDirtyFilter($this->_db, $this->authUserId);
	        	
				// New comment tracking
				$lastCommentDate = DatabaseObject_Comment::getLastCommentDate($this->_db, array('rsrc_id' => $this->_id));

				$track_id = DatabaseObject_CommentTracking::getTrackingId($this->_db, $options);
				
				if ($track_id) {
				    $track = new DatabaseObject_CommentTracking($this->_db);
				    $track->load($track_id);

				    if ($track->date_last_updated < $lastCommentDate) {
    						$this->meta->newComments = true;
                            $this->meta->lastReadComment = $track->comment_num;
                            $this->meta->lastReadCommentUser = DatabaseObject_User::getUserNameById($this->_db, array('user_id' => $track->comment_user_id));
                            $this->meta->lastReadCommentDate = common::getRelativeTime($track->date_last_updated);
    						$this->meta->lastReadPage = ceil($this->meta->lastReadComment / Zend_Registry::get('paginationConfig')->CommentsPerPage);
    				}
				}
				
				
				
				
				// Distance (experimental)
/*	            $this->distanceFrom = UtilityController::getHaversineDistance($this->locationsArray[0]['latitude'],
	            															  $this->locationsArray[0]['longitude'],
	            															  $this->identity->latitude,
	            															  $this->identity->longitude,
	            															  'Mi');
*/	            
			}
			
        	// Dirty words filtered by user or default?
        	if ($this->filterDirty != 'off') {
        		$this->title = UtilityController::cleanDirtyWords($this->title);
				$this->descrip = UtilityController::cleanDirtyWords($this->descrip);
			}

        }//postLoad

        protected function postInsert()
        {
			// This saves the new resource to the resource_url table
			// for nice SEO urls
			$this->resourceSeoUrl->rsrc_id = $this->_id;
			
			$this->resourceSeoUrl->rsrc_url = $this->generateUniqueUrl($this->title);
			
			$this->resourceSeoUrl->save(false);
			
			// This saves the new resource image if any
			// Move the files from temp if any?
			$imagePreview = new Zend_Session_Namespace('submitPreview');
			//$this->image->uploadFile(Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$imagePreview->tempFilename);			
			
			// Save/Update to the db
			// if a filename exists (the user uploaded a file)
			if ($imagePreview->filename) {
				$this->image->rsrc_id = $this->_id;
				$this->image->filename = $imagePreview->filename;
				$this->image->caption = $imagePreview->caption;
				$this->image->save(false);
			
				if(!copy(Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$imagePreview->tempFilename,
						Zend_Registry::get('imageConfig')->resourceImagePath.DIRECTORY_SEPARATOR.$this->image->getId())) {
					return false;
					}

				// remove current preview image session
				$imagePreview = new Zend_Session_Namespace('submitPreview');
				// TODO: Delete the file from temp
				unlink(Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$imagePreview->tempFilename);
				unset($imagePreview->filename);
				unset($imagePreview->tempFilename);

			}
			
            // If this is an event resource being added
            // let's insert that stuff
            if($this->getResourceTypeIdByCategoryId($this->_db, $this->cat_id) == Zend_Registry::get('resourceConfig')->event) {
            	if(!DatabaseObject_EventDate::saveEventDates($this->_db, $this->_id,$this->repetition,$this->start_date,$this->duration,$this->end_date))
	            	return false;
            }
	            
	            // Create a new field in Resources to hold the duration parameter.
            
			return true;
        }

        protected function postUpdate()
        {
			if(is_object($this->extended)) {
        		$this->extended->save(false);
            }

        	// This saves the new resource to the resource_url table
			// for nice SEO urls
			$id = $this->getSeoUrlById($this->_db,$this->_id);
			$this->resourceSeoUrl->load($id);
			
			$this->resourceSeoUrl->rsrc_url = $this->generateUniqueUrl($this->title);
			$this->resourceSeoUrl->save(false);

			// This saves the new resource image if any
			// Move the files from temp if any?
			$imagePreview = new Zend_Session_Namespace('submitPreview');
			//$this->image->uploadFile(Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$imagePreview->tempFilename);			
			
			// Save/Update to the db
			// if a filename exists (the user uploaded a file)
			if ($imagePreview->filename) {
			    $this->image->rsrc_id = $this->_id;
				$this->image->filename = $imagePreview->filename;
				$this->image->caption = $imagePreview->caption;
				$this->image->save(false);
			
				if(!copy(Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$imagePreview->tempFilename,
						Zend_Registry::get('imageConfig')->resourceImagePath.DIRECTORY_SEPARATOR.$this->image->getId()))
				return false;

				// remove current preview image session
				$imagePreview = new Zend_Session_Namespace('submitPreview');
				unlink(Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$imagePreview->tempFilename);
				unset($imagePreview->filename);
				unset($imagePreview->tempFilename);
				unset($imagePreview->caption);

			} else {
				// Just update the caption for the image
			    if ($imagePreview->caption) {
    				$this->image->caption = $imagePreview->caption;
    				$this->image->save(false);
			    }
			}

			// If this is an event resource being updated
            if($this->getResourceTypeIdByCategoryId($this->_db, $this->cat_id) == Zend_Registry::get('resourceConfig')->event) {
            	// Remove all old dates
				DatabaseObject_EventDate::deleteEventDates($this->_db, array('rsrc_id' => $this->getId()));
            	
            	// Add the new dates
            	if(!DatabaseObject_EventDate::saveEventDates($this->_db, $this->_id,$this->repetition,$this->start_date,$this->duration,$this->end_date))
	            	return false;
            }
			return true;
        }

        protected function preUpdate()
        {
			return true;
        }

        protected function preDelete()
        {
        	// remove the associated data from the resource_url table
        	$this->_db->delete(Zend_Registry::get('dbTableConfig')->tblResourceUrl, "rsrc_id = " . $this->getId());
        		
        	// remove the associated data from the resource_image table
			$this->_db->delete(Zend_Registry::get('dbTableConfig')->tblResourceImage, "rsrc_id = " . $this->getId());

        	// remove the associated data from all users user_resource_notify table
			$this->_db->delete("user_resource_notify", "rsrc_id = " . $this->getId() );

            // remove the extended info
			if(is_object($this->extended)) {
            	$this->extended->delete();
			}
        	return true;
        }
        
        /**
         * Returns the closed status of the resource
         * closed or open
         *
         * @return bool
         */
        public function isClosed() {
        	
        	return (bool) $this->closed;
        }
        
        /**
         * Returns the active status of the resource
         * active or not
         *
         * @return bool
         */
        public function isActive() {
        	
        	return $this->is_active;
        }
        
        /**
         * Returns the date the comment was edited
         *
         * @return date string
         */
        public function getEditDate()
        {
            //Zend_Debug::dump($this->date_edited);die;
        	return common::neatDateTime($this->date_edited);
        }

        /**
         * Inserts a unique URL for the resource
         * in the resource_url table
         *
         * @param unknown_type $title
         * @return unknown
         */
		protected function generateUniqueUrl($title)
        {
            $url = strtolower($title);

            $filters = array(
                // replace & with 'and' for readability
                '/&+/' => 'and',

                // replace non-alphanumeric characters with a hyphen
                '/[^a-z0-9]+/i' => '-',

                // replace multiple hyphens with a single hyphen
                '/-+/'          => '-'
            );


            // apply each replacement
            foreach ($filters as $regex => $replacement)
                $url = preg_replace($regex, $replacement, $url);

            // restrict the length of the URL
            $url = trim(substr($url, 0, 30));

            // remove hyphens from the start and end of string
            $url = trim($url, '-');

            // set a default value just in case
            if (strlen($url) == 0)
                $url = 'post';


            // find similar URLs
            $query = sprintf("select rsrc_url from %s where rsrc_url like ?",
                             'resource_url');

            $query = $this->_db->quoteInto($query, $url. '%');
            $result = $this->_db->fetchCol($query);


            $updateURL = false;
            
            // if no matching URLs then return the current URL
            if (count($result) == 0 || !in_array($url, $result)) {
                return $url;
            }
            
            // generate a unique URL
            $i = 2;
            do {
                $_url = $url . '-' . $i++;
            } while (in_array($_url, $result));

            return $_url;

            // No change to the title so leave it alone
//            if ($result[0] == $url) {
//                return $url;
//            }
            
        }
        
        /**
         * Gets all the resource data based on
         * resource type and/or category
         *
         * @param object $db
         * @param array $options
         * @return resource object
         */
        public static function getResources($db, $options = array())
        {
        	// initialize the options
        	$defaults = array(
        		'offset'          => 0,
        		'limit'           => 0,
        		'order'           => '',
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // Valid Orders for PHP sorting
            $validOrder = array('rsrc_date',
                                'votes',
                                'views_lifetime',
                                'count_comments',
                                'date_last_active'
                                );
                                
            // Is this a valid order?
			if (in_array($options['order'], $validOrder)) {
			    
                // Resource caching check
                $memcache = new Memcache;
    		    $memcache->connect("localhost",11211);
    		    
    		    // Define keys
    	        $masterKey = DatabaseObject_Resource::getResourceCacheId();

    	        if (!empty($options['bookmark']) && !empty($options['user_key'])) {
    		        // Create Bookmark memcache key
        		    $cacheKey = "{$masterKey}:{$options['user_key']}:bookmarks";
    		    } else {
    		        // Create Resource Key
        		    $rsrcKey = (!empty($options['rsrc_type_id'])) ? $options['rsrc_type_id'] : 'all';
        		    $catKey = (!empty($options['cat_id'])) ? $options['cat_id'] : 'all';
        		    $rangeKey = (!empty($options['range'])) ? $options['range'] : '30days';
        		    $distanceKey = (!empty($options['distance'])) ? md5($options['distance']) : 'all';
        		    $userKey = (!empty($options['user_key'])) ? $options['user_key'] : 'allUsers';
        		    $actionKey = (!empty($options['action'])) ? $options['action'] : 'generic';
        	        
        	        $cacheKey = "{$masterKey}:resources:{$actionKey}:{$rsrcKey}:{$catKey}:{$rangeKey}:{$distanceKey}";
    		    }
    		    
    		    // Special case for the Last Visited link
    		    if (!empty($options['last_visit_user_id'])) {
    		        $cacheKey .= ":{$options['last_visit_user_id']}";
    		    }
    		    
    		    if(!$data = $memcache->get($cacheKey)) {
    		        // Do Regular Resource Query
                        
                    // Cache miss: Get the resources
//        	        Zend_Debug::dump('cache miss!'); // This should be logged
                    //if ($_SERVER['REMOTE_ADDR'] == '67.180.50.178') {
                    //    Zend_Debug::dump("New cacheKey = {$cacheKey}");
                    //}

                    // run the base query
                    $select = self::_getBaseQuery($db, $options);
                    
                    // set the fields to select
                    $select->from(null, 
                    				array(  'r.rsrc_id',
        									'r.user_id',
        									'r.title',
        									'r.descrip',
        									'r.last_comment_id',
        									'r.url',
        									'r.rsrc_date',
        									'r.date_last_active',
        									'r.date_edited',
        									'r.start_date',
        									'r.end_date',
        									'r.closed',
        									'r.is_active',
        									'r.count_comments',
        									'r.views_lifetime',
        									'r.votes'
        									)
                    				)
                    	   ->join(array('c' => 'category'),
                    			  'r.cat_id = c.cat_id',
                    			   		array('cat_id')
                    			 )
                    	   ->join(array('rt' => 'resource_type'),
                    			  'c.rsrc_type_id = rt.rsrc_type_id',
                    			   		array('rsrc_type_id')
                    			 )
                    	   ->join(array('u' => 'user'),
                    			  'r.user_id = u.user_id',
                    			   		array('user_name')
                    			 );
                    
                    // If this is an EVENT and the user wants distance browsing we must add the following
                    // to our select object:
                    if (!empty($options['distance'])) {
        		            //Zend_Debug::dump($options['distance']);die;
                    		$select->join(array('l' => 'location'),
                    				'r.rsrc_id = l.rsrc_id AND l.primary_location = 1',
                    						array('latitude',
                    							  'longitude')
                    						)
                    				->where($options['distance']);
                    }
    
                    // fetch post data from the db
                    //Zend_Debug::dump($options);
                    //Zend_Debug::dump($select->__toString());die;
        
        	        //$before = memory_get_usage();
    
        	        $data = $db->fetchAll($select);
        	        
                    //$after = memory_get_usage();
                    //Zend_Debug::dump(round(($after - $before)/1048576,2)." megabytes");
    
        	        $memcache->set($cacheKey, $data, 0, DatabaseObject_Resource::RSRC_CACHE_KEY_TIME);
    		    } else {
//        	        Zend_Debug::dump('cache HIT!'); // This should be logged
//                    Zend_Debug::dump("cacheKey = {$cacheKey}");
    		    }

    	        // Call the Resource ORDER function
    	        self::processResourceOrder($data, $options['order']);
    	        
    	        // if this is activity ordered on the all tab,  remove any topic that is an event and
    	        // has no comment count.
    	        //Zend_Debug::dump($options['order'] == 'date_last_active' && empty($options['rsrc_type_id']));
    	        if ($options['order'] == 'date_last_active' && empty($options['rsrc_type_id'])) {
        	        foreach ($data as $key => $value) {
        	            if ($value['rsrc_type_id'] == Zend_Registry::get('resourceConfig')->event && $value['count_comments'] == 0) {
        	                unset($data[$key]);
        	            }
        	        }
    	        }
                
    	        //Initialize the $resources array and grab the topic count
                $resources = array();
                $resources['count'] = count($data);
    	        
    	        // limit in php
    	        $data = array_slice($data, $options['offset'], $options['limit']);
    
                // turn data into array of DatabaseObject_Resource objects
                $resources['topics'] = self::BuildMultiple($db, __CLASS__, $data);
                $resource_ids = array_keys($resources['topics']);
                
                if(count($resource_ids) == 0)
                	return array( 'topics' => array(),
                	              'count'  => 0
                	             );
                	
                //Zend_Debug::dump($resources);die();
		    }
	        //Zend_Debug::dump($resources);

		    return $resources;
        } //getResources
        
        /**
         * Special method for handling the thousands of
         * resources we have when the user selects
         * the All Time option
         *
         * @param db object $db
         * @param array of $options
         * @return resource objects
         */
        public static function getAllTime($db, $options = array())
        {
        	// initialize the options
        	$defaults = array(
        		'offset'          => 0,
        		'limit'           => 0,
        		'order'           => '',
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // Resource caching check
            $memcache = new Memcache;
		    $memcache->connect("localhost",11211);
		    
		    // Define keys
		    $rsrcKey = (!empty($options['rsrc_type_id'])) ? $options['rsrc_type_id'] : 'all';
		    $catKey = (!empty($options['cat_id'])) ? $options['cat_id'] : 'all';
		    $rangeKey = (!empty($options['range'])) ? $options['range'] : '30days';
		    $orderKey = (!empty($options['order'])) ? $options['order'] : 'rsrc_date';
		    $distanceKey = (!empty($options['distance'])) ? md5($options['distance']) : 'all';
		    //$userKey = (!empty($options['user_key'])) ? $options['user_key'] : 'allUsers';
		    $pageKey = (!empty($options['page'])) ? $options['page'] : 1;
		    $actionKey = (!empty($options['action'])) ? $options['action'] : 'generic';
	        $masterKey = DatabaseObject_Resource::getResourceCacheId();
	        
	        $cacheKey = "{$masterKey}:getAllResources:{$actionKey}:{$rsrcKey}:{$catKey}:{$rangeKey}:{$orderKey}:{$pageKey}:{$distanceKey}";
		    
		    if(!$results = $memcache->get($cacheKey)) {
                
		        // Cache miss: Get the resources
//    	        Zend_Debug::dump('AllTime cache miss!'); // This should be logged
//                Zend_Debug::dump("New cacheKey = {$cacheKey}");

                // run the base query
                $select = self::_getBaseQuery($db, $options);
                
                // set the fields to select
                $select->from(null, 
                				array(  'r.rsrc_id',
        								)
        					 )
                    	   ->join(array('c' => 'category'),
                    			  'r.cat_id = c.cat_id',
                    			   		array('cat_id')
                    			 )
                    	   ->join(array('rt' => 'resource_type'),
                    			  'c.rsrc_type_id = rt.rsrc_type_id',
                    			   		array('rsrc_type_id')
                    			 );
                
                // If this is an EVENT and the user wants distance browsing we must add the following
                // to our select object:
                if (!empty($options['distance'])) {
    		            //Zend_Debug::dump($options['distance']);die;
                		$select->join(array('l' => 'location'),
                				'r.rsrc_id = l.rsrc_id AND l.primary_location = 1',
                						array('latitude',
                							  'longitude')
                						)
                				->where($options['distance']);
                }
    
                // set the offset, limit, and ordering of results
                if ($options['limit'] > 0) {
                	$select->limit($options['limit'], $options['offset']);
                }
                	
                $select->order($options['order'] . ' DESC');
                $select->order('r.rsrc_date DESC');
    
                // fetch post data from the db
//                Zend_Debug::dump($options);
                //Zend_Debug::dump($select->__toString());die;
    	        $results = $db->fetchAll($select);
	        
    	        $memcache->set($cacheKey, $results, 0, DatabaseObject_Resource::RSRC_CACHE_KEY_TIME);
		    } else {
//    	        Zend_Debug::dump('AllTime cache HIT!'); // This should be logged
//                Zend_Debug::dump("cacheKey = {$cacheKey}");
		    }

		    $idArray = array();
		    $idArray['rsrc_id'] = array();
		    
		    foreach ($results as $value) {
		         $idArray['rsrc_id'][] = $value['rsrc_id'];
		    }
	        //Zend_Debug::dump($idArray);die;
	        $idArray['order'] = $options['order'] . ' DESC';
	        $idArray['limit'] = $options['limit'];

	        //Zend_Debug::dump($idArray);die;
	        
	        // Retrieve the resources with getResourceById() method
	        $resources = DatabaseObject_Resource::getResourceById($db, $idArray);
	        
	        //Zend_Debug::dump($resources);die;
	        
	        // turn data into array of DatabaseObject_Resource objects
            //$resources = self::BuildMultiple($db, __CLASS__, $data);
            //$resource_ids = array_keys($resources);
            
            if(count($resources) == 0) {
            	return array();
            }
            	
            return $resources;
        }
        
        /**
         * Takes the data array and re-orders it to the
         * specified criteria for display
         *
         * @param data array $data
         * @param order by $order
         */
        public static function processResourceOrder(&$data, $order = 'rsrc_date')
        {
            // Order the resource array
            switch ($order) {
                case 'rsrc_date':
        	        $sortOptions = array($order => SORT_DESC, 'rsrc_date' => SORT_DESC);
        	        break;
    	        
                case 'votes':
        	        $sortOptions = array($order => SORT_DESC, 'rsrc_date' => SORT_DESC);
        	        break;
    	        
                case 'views_lifetime':
        	        $sortOptions = array($order => SORT_DESC, 'rsrc_date' => SORT_DESC);
        	        break;
    	        
                case 'count_comments':
        	        $sortOptions = array($order => SORT_DESC, 'rsrc_date' => SORT_DESC);
        	        break;
    	        
                case 'date_last_active':
        	        $sortOptions = array($order => SORT_DESC, 'rsrc_date' => SORT_DESC);
        	        break;
    	        
                default:
        	        $sortOptions = array($order => SORT_DESC, 'rsrc_date' => SORT_DESC);
            }
	        $data = Sol_Array_Sort::multiSort($data, $sortOptions);
        }
        
        /**
         * Takes the data array and re-orders it to the
         * specified criteria for display.
         * 
         * This is a hack and should probably go away in the next version of the dash???
         *
         * @param data array $data
         * @param order by $order
         */
        public static function processOldDashActivity(&$data)
        {
            $catIds = array(9, 10, 11,13, 14);
            foreach ($data as $key => $value)
            {
                if (in_array($value['cat_id'], $catIds) && $value['count_comments'] < 1)
                {
                    unset($data[$key]);
                }
            }
        }
        
        /**
         * Returns the previous and next rsrc_ids for a given rsrc_id
         * Works with the discussion bar session config. Whatever the
         * user had as their discussion bar config is how this figures
         * out the next/previous rsrc_ids. If none exist, a default is set.
         *
         * @param db object $db
         * @param array of $options
         * @return array('prev' and 'next') if any.
         */
        public static function getPrevNextLinks($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'          => 0,
        		'limit'           => 0,
        		'order'           => '',
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
			    
	        // Do Regular Resource Query
                
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array(  'r.rsrc_id',
									'r.rsrc_date',
									'r.count_comments',
									'r.date_last_active',
									'r.votes',
									'r.views_lifetime'
									)
            				)
                	   ->join(array('c' => 'category'),
                			  'r.cat_id = c.cat_id',
                			   		array('cat_id')
                			 )
                	   ->join(array('rt' => 'resource_type'),
                			  'c.rsrc_type_id = rt.rsrc_type_id',
                			   		array('rsrc_type_id')
                			 );
            
            // If this is an EVENT and the user wants distance browsing we must add the following
            // to our select object:
            if (!empty($options['distance'])) {
		            //Zend_Debug::dump($options['distance']);die;
            		$select->join(array('l' => 'location'),
            				'r.rsrc_id = l.rsrc_id AND l.primary_location = 1',
            						array('latitude',
            							  'longitude')
            						)
            				->where($options['distance']);
            }

            // fetch post data from the db
            //Zend_Debug::dump($options);
            //Zend_Debug::dump($select->__toString());

	        $data = $db->fetchAll($select);
	        
	        // Call the Resource ORDER function
	        self::processResourceOrder($data, $options['order']);
	        
	        // locate the current ID and pull the previous and next topics
	        $links = array();
            $lastIteration = false;

            foreach ($data as $key => $value) {
	            if ($lastIteration) {
	                $links['next'] = $value['rsrc_id'];
	                break;
	            }

	            if ($value['rsrc_id'] == $options['current_id']) {
	                $lastIteration = true;
	                continue;
	            }

	            $links['prev'] = $value['rsrc_id'];
	        }
	        //Zend_Debug::dump($links);die;
	        if (!$lastIteration) {
	            return array();
	        }
	        
	        return $links;
        }
        
        /**
         * Get the count for pagination, etc...
         *
         * @param db object $db
         * @param array $options
         * @return int
         */
        public static function getResourceCount($db, $options)
        {
            // Resource caching check
            $memcache = new Memcache;
		    $memcache->connect("localhost",11211);
		    
		    // Define keys
	        $masterKey = DatabaseObject_Resource::getResourceCacheId();

	        if (!empty($options['bookmark']) && !empty($options['user_key'])) {
		        // Create Bookmark memcache key
    		    $cacheKey = "{$masterKey}:{$options['user_key']}:bookmarksCount";
		    } else {
		        // Create Resource Key
    		    $rsrcKey = (!empty($options['rsrc_type_id'])) ? $options['rsrc_type_id'] : 'all';
    		    $catKey = (!empty($options['cat_id'])) ? $options['cat_id'] : 'all';
    		    $rangeKey = (!empty($options['range'])) ? $options['range'] : '30days';
    		    $distanceKey = (!empty($options['distance'])) ? md5($options['distance']) : 'all';
    		    $actionKey = (!empty($options['action'])) ? $options['action'] : 'generic';
    	        
    	        $cacheKey = "{$masterKey}:getResourceCount:{$actionKey}:{$rsrcKey}:{$catKey}:{$rangeKey}:{$distanceKey}";
		    }

		    // Special case for the Last Visited link
		    if (!empty($options['last_visit_user_id'])) {
		        $cacheKey .= ":{$options['last_visit_user_id']}";
		    }
		    
		    if(!$count = $memcache->get($cacheKey)) {
		        // Cache miss: Get the resources
//    	        Zend_Debug::dump('count cache MISS!'); // This should be logged
//                Zend_Debug::dump("New cacheKey = {$cacheKey}");

                $select = self::_getBaseQuery($db, $options);
                $select->from(null, 'count(*)')
                	   ->join(array('c' => 'category'),
                			  'r.cat_id = c.cat_id', array())
                	   ->join(array('rt' => 'resource_type'),
                			  'c.rsrc_type_id = rt.rsrc_type_id', array()
                			  );
                	   //->join(array('u' => 'user'),
                		//	  'r.user_id = u.user_id', array()
                		//	 );
    
                // If this is an EVENT and the user wants distance browsing we must add the following
                // to our select object:
                if (!empty($options['distance'])) {
    		            //Zend_Debug::dump($options['distance']);die;
                		$select->join(array('l' => 'location'),
                				'r.rsrc_id = l.rsrc_id AND l.primary_location = 1',
                						array()
                						)
                				->where($options['distance']);
                }
    
                //Zend_Debug::dump($options);
                //Zend_Debug::dump($select->__toString());die;
                $count = (int) $db->fetchOne($select);
    	        $memcache->set($cacheKey, $count, 0, DatabaseObject_Resource::RSRC_CACHE_KEY_TIME);
		    } else {
//    	        Zend_Debug::dump('count cache HIT!'); // This should be logged
//                Zend_Debug::dump("cacheKey = {$cacheKey}");
		    }
		    
		    return (int) $count;
        } // getResourceCount

        /**
         * Get a resource by the Id
         *
         * @param db object $db
         * @param array $options
         * @return object
         */
        public static function getResourceById($db, $options)
        {
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> ''
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_getBaseQuery($db, $options);
            $select->from(null,
            				array(  'r.rsrc_id',
									'r.user_id',
									'r.cat_id',
									'r.title',
									'r.descrip',
									'r.last_comment_id',
									'r.url',
									'r.rsrc_date',
									'r.closed',
									'r.date_last_active',
									'r.start_date',
									'r.end_date',
									'r.is_active'
									)
								)
            	   ->join(array('c' => 'category'),
            			  'r.cat_id = c.cat_id',
            			   		array('cat_id')
            			 )
            	   ->join(array('rt' => 'resource_type'),
            			  'c.rsrc_type_id = rt.rsrc_type_id',
            			   		array('rsrc_type_id')
            			 )
            	   ->join(array('u' => 'user'),
            			  'r.user_id = u.user_id',
            			   		array('user_name')
            			 );

            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);

//            Zend_Debug::dump($options);
//            Zend_Debug::dump($select->__toString());die;
            $data = $db->fetchAll($select);

			//Zend_Debug::dump($data);die;
            // turn data into array of DatabaseObject_Resource objects
            $resources = self::BuildMultiple($db, __CLASS__, $data);
            $resource_ids = array_keys($resources);
            
            if(count($resource_ids) == 0)
            	return array();
            	
            return $resources;
        } // getResourceById()

        /**
         * Gets all the events
         *
         * @param object $db
         * @param array $options
         * @return resource object
         */
        public static function getEvents($db, $options = array())
        {
        	// initialize the options
        	$defaults = array(
        		'offset'          => 0,
        		'limit'           => 0,
        		'order'           => '',
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // Valid Orders for PHP sorting
            $validOrder = array('rsrc_date',
                                'votes',
                                'views_lifetime',
                                'count_comments'
                                );
			
            // Event caching check
            $memcache = new Memcache;
		    $memcache->connect("localhost",11211);
		    
	        $masterKey = DatabaseObject_Resource::getResourceCacheId();
		    
		    if(!$data = $memcache->get("{$masterKey}:event:" . md5(serialize($options)))) {

                // Cache miss: Get the event resources
    	        //Zend_Debug::dump('cache miss!'); // This should be logged
                //Zend_Debug::dump("New cacheKey = {$masterKey}:event:" . md5(serialize($options)));
                
                // run the base query
                $select = self::_getBaseQuery($db, $options);
                
                // set the fields to select
                $select->from(null, 
                				array(  'r.rsrc_id',
    									'r.user_id',
    									'r.title',
    									'r.descrip',
    									'r.last_comment_id',
    									'r.url',
    									'r.rsrc_date',
    									'r.date_edited',
    									'r.start_date',
    									'r.end_date',
    									'r.closed',
    									'r.is_active',
    									'r.count_comments',
    									'r.votes'
    									)
                				)
                	   ->join(array('c' => 'category'),
                			  'r.cat_id = c.cat_id',
                			   		array('cat_id')
                			 )
                	   ->join(array('rt' => 'resource_type'),
                			  'c.rsrc_type_id = rt.rsrc_type_id',
                			   		array('rsrc_type_id')
                			 )
                	   ->join(array('u' => 'user'),
                			  'r.user_id = u.user_id',
                			   		array('user_name')
                			 );
                
                // If this is an EVENT and the user wants distance browsing we must add the following
                // to our select object:
                if (!empty($options['distance'])) {
    		            //Zend_Debug::dump($options['distance']);die;
                		$select->join(array('l' => 'location'),
                				'r.rsrc_id = l.rsrc_id AND l.primary_location = 1',
                						array('latitude',
                							  'longitude')
                						)
                				->where($options['distance']);
                }
                			 
                if (isset($options['order'])) {
                    if (!in_array($options['order'], $validOrder)) {
                        $select->order($options['order']);
                    }
                }
                
                // fetch post data from the db
                //Zend_Debug::dump($options);
                //Zend_Debug::dump($select->__toString());die;
    
    	       // $before = memory_get_usage();

    	        $data = $db->fetchAll($select);
    	        
                //$after = memory_get_usage();
                //Zend_Debug::dump(round(($after - $before)/1048576,2)." megabytes");

                //Zend_Debug::dump($data);die;
    	        $memcache->set("{$masterKey}:event:" . md5(serialize($options)), $data, 0, 300);
		    }

            // turn data into array of DatabaseObject_Resource objects
            $resources = self::BuildMultiple($db, __CLASS__, $data);
            $resource_ids = array_keys($resources);
            
            if(count($resource_ids) == 0)
            	return array();
            	
            //Zend_Debug::dump($resources);die();
            return $resources;
        } //getResources
        

        /**
         * Gets the latest resources
         * mostly for the Latest activity
         * module
         *
         * @param object $db
         * @param array $options
         * @return resource ids array
         */
        public static function getLatestResources($db, $options = array())
        {
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 400,
        		'order'		=> 'rsrc_date DESC'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // instantiate a Zend select object
            $select = $db->select();
            
            // define the tables to pull from
            $select->from(array('r' => 'resource'), array());
                        
            // set the fields to select
            $select->from(null, 
            				array(  'r.rsrc_id',
									'r.rsrc_date'
									)
            			 );
            
            // filter results on specified resource id (if any)
            if (!empty($options['rsrc_id'])) {
                $select->where('r.rsrc_id in (?)', $options['rsrc_id']);
            }

            // filter results on specified category type (if any)
            if (!empty($options['cat_id']))
                $select->where('r.cat_id in (?)', $options['cat_id']);

            // exclude specified category types
            if (!empty($options['exclude_cat_id']))
                $select->where('r.cat_id not in (?)', $options['exclude_cat_id']);

            // filter results on specified r.rsrc_date (if any)
            if (!empty($options['rsrc_date']))
                $select->where('r.rsrc_date >= ?', $options['rsrc_date']);

             // set the offset, limit, and ordering of results
            if (!empty($options['limit']))
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;

	        $data = $db->fetchAll($select);

	        return $data;
        } //getLatestResources

        /**
         * Returns the number of resources
         * created by a user_id
         * 
         * (ACTIVE)
         *
         * @param database object $db
         * @param array $options
         * @return int
         */
        public static function getUserResourceCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)');

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Returns the number of draft resources
         * saved by a user_id
         * 
         * (NOT ACTIVE)
         *
         * @param database object $db
         * @param array $options
         * @return int
         */
        public static function getUserDraftCount($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 'count(*)')
            
            	   ->join(array('c' => 'category'),
            			  'r.cat_id = c.cat_id',
            			   		array()
            			 )
            	   ->join(array('rt' => 'resource_type'),
            			  'c.rsrc_type_id = rt.rsrc_type_id',
            			   		array()
            			 )
            	   ->join(array('u' => 'user'),
            			  'r.user_id = u.user_id',
            			   		array()
            			 )
            	   ->where('r.is_active = ' . self::STATUS_DRAFT
            			 );

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        /**
         * Returns All draft resources for a user
         * 
         *
         * @param db object $db
         * @param array $options
         * @return user id(s) or false
         */
        public static function getAllDraftsByUserId($db, $options)
        {
        	// initialize the options
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0
        	);

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
            
            // run the base query
            $select = self::_getBaseQuery($db, $options);
            
            // set the fields to select
            $select->from(null, 
            				array('rsrc_id'
								)
							)
            	   ->where('r.is_active = ' . self::STATUS_DRAFT
            	   			);            
            // set the offset, limit, and ordering of results
            if ($options['limit'] > 0)
            	$select->limit($options['limit'], $options['offset']);
            	
            $select->order($options['order']);
            
            // fetch post data from the db
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);

            return $result;
        }
        
        
        /**
         * Base query from which all querys are born!
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        private static function _getBaseQuery($db, $options)
        {
            // initialize the options
            $defaults = array(
                'rsrc_id' => array(),
                'from'    => '',
                'to'      => ''
            );

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            // create a query that selects FROM the resource table
            
            // instantiate a Zend select object
            $select = $db->select();
            
            // define the tables to pull from
            $select->from(array('r' => 'resource'), array());
                        
            // filter the records based on the start and finish dates
            if (strlen($options['from']) > 0) {
                $ts = strtotime($options['from']);
                $select->where('r.rsrc_date >= ?', date('Y-m-d H:i:s', $ts));
            }

            if (strlen($options['to']) > 0) {
                $ts = strtotime($options['to']);
                $select->where('r.rsrc_date <= ?', date('Y-m-d H:i:s', $ts));
            }

            // filter results on specified resource type (if any)
            if (!empty($options['rsrc_type_id']))
                $select->where('rt.rsrc_type_id in (?)', $options['rsrc_type_id']);

            // filter results on specified category type (if any)
            if (!empty($options['cat_id']))
                $select->where('c.cat_id in (?)', $options['cat_id']);

            // filter results on specified user id (if any)
            if (!empty($options['user_id']))
                $select->where('r.user_id in (?)', $options['user_id']);

            // filter results on specified resource id (if any)
            if (count($options['rsrc_id']) > 0) {
                $select->where('r.rsrc_id in (?)', $options['rsrc_id']);
            }

            // filter results on specified r.rsrc_date (if any)
            if (!empty($options['rsrc_date']))
                $select->where('r.rsrc_date >= ?', $options['rsrc_date']);

            // filter results on specified r.rsrc_date (if any)
            if (!empty($options['now_date']))
                $select->where('r.rsrc_date < ?', $options['now_date']);

            // filter results on specified r.date_last_active_start (if any) THIS IS FOR ACTIVITY SORTING
            if (!empty($options['date_last_active_start']))
                $select->where('r.date_last_active >= ?', $options['date_last_active_start']);

            // filter results on specified r.date_last_active_end (if any) THIS IS FOR ACTIVITY SORTING
            if (!empty($options['date_last_active_end']))
                $select->where('r.date_last_active < ?', $options['date_last_active_end']);

            // filter results on specified event specific r.start_date (if any)
            if (!empty($options['start_date']))
                $select->where('r.start_date >= ?', $options['start_date']);

            // filter results on specified event specific r.start_date (if any)
            if (!empty($options['after_date']))
                $select->where('r.start_date < ?', $options['after_date']);

            // filter results on ACTIVE or DRAFT (if any)
            if (!empty($options['is_active']))
                $select->where('r.is_active = ?', $options['is_active']);

            // exclude specified category types
            if (!empty($options['exclude_cat_id'])) {
                $select->where('r.cat_id not in (?)', $options['exclude_cat_id']);
            }

            //Zend_Debug::dump($select->__toString());die;
            return $select;
        }//_getBaseQuery

		/**
		 * Deletes a draft from the database
		 *
		 * @param database $db
		 * @param array $options
		 * @return bool
		 */
        public static function deleteDraft($db, $options)
		{
			// get the resource
			$draft = new DatabaseObject_Resource($db);
			$draft->load($options['rsrc_id']);
			
			if($draft->user_id == $options['user_id'] && $draft->is_active == self::STATUS_DRAFT ) {
				
				// delete it
				$draft->delete();

				return true;
			}
		return false;
		}
        
        /**
		 * This little guy simply pulls the
		 * SEO url for the given
		 * rsrc_id
		 *
		 * @param database object $db
		 * @param rsrc_id $id
		 * @return string
		 */
        public static function getResourceUrl($db, $id = 0)
		{
             // instantiate a Zend select object
            $select = $db->select();
            $select->from('resource_url', 'rsrc_url')
            	   ->where('rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
		}
		
        /**
         * Pulls the rsrc_id, comment_num and resource_url
         * from a given comment_id
         * 
         * Used mostly in redirecting from the old
         * Yehoodi
         *
         * @param object $db
         * @param int $comment_id
         * @return array
         */
		public static function getResourceUrlByCommentId($db, $id = 0)
		{
             // instantiate a Zend select object
            $select = $db->select();
            $select->from(array('c' => 'comment'), array('rsrc_id','comment_num'))
            	   ->join(array('ru' => 'resource_url'),'c.rsrc_id = ru.rsrc_id', array('ru.rsrc_url'))
            	   ->where('c.comment_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchAll($select);
		}
		
		/**
		 * This one pulls the url_id #
		 * for updating the resource
		 *
		 * @param database object $db
		 * @param resource id $id
		 * @return string
		 */
        public static function getSeoUrlById($db, $id)
		{
             // instantiate a Zend select object
            $select = $db->select();
            $select->from('resource_url', 'url_id')
            	   ->where('rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
		}
		
		/**
		 * Get the event repetition from the resource
		 *
		 * @param database object $db
		 * @param resource id $id
		 * @return string
		 */
        public static function getRepetitionById($db, $id)
		{
             // instantiate a Zend select object
            $select = $db->select();
            $select->from('resource', 'repetition')
            	   ->where('rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
		}
		
        /**
         * Gets the resource name from the category id
         *
         * @param object $db
         * @param int $cat_id
         */
        public static function getResourceNameByCategoryId($db, $id)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the category Name
            $select->from('category',array())
            	   ->join('resource_type','resource_type.rsrc_type_id = category.rsrc_type_id', array('rsrc_type'))
            	   ->where('cat_id = ?', $id);

            
            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }//getResourceNameByCategoryId

        /**
         * Gets the resource name from the category url string
         *
         * @param object $db
         * @param string $cat_site_url
         */
        public static function getResourceNameByCategoryUrl($db, $url)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the category Name
            $select->from('category',array())
            	   ->join('resource_type','resource_type.rsrc_type_id = category.rsrc_type_id', array('rsrc_type'))
            	   ->where('cat_site_url = ?', $url);

            
            //Zend_Debug::dump($select->__toString());
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }//getResourceNameByCategoryUrl

        /**
         * Gets the resource type id from a given
         * category url
         *
         * @param db object $db
         * @param string $url
         * @return rsrc type id
         */
        public static function getResourceIdByCategoryUrl($db, $url)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the category Name
            $select->from('category',array())
            	   ->join('resource_type','resource_type.rsrc_type_id = category.rsrc_type_id', array('rsrc_type_id'))
            	   ->where('cat_site_url = ?', $url);

            
            //Zend_Debug::dump($select->__toString());
        	$result = $db->fetchOne($select);
        	
        	if (!$result) {
	        	return 0;
        	} else {
        		return $result;
        	}
        }//getResourceNameByCategoryUrl

        /**
         * Gets the resource id from the category id
         *
         * @param object $db
         * @param int $cat_id
         */
        public static function getResourceTypeIdByCategoryId($db, $id)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the resource Id
            $select->from('category',array())
            	   ->join('resource_type','resource_type.rsrc_type_id = category.rsrc_type_id', array('rsrc_type_id'))
            	   ->where('cat_id = ?', $id);

            
            //Zend_Debug::dump($select->__toString());
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }//setResourceAndCategoryName

        /**
         * Gets the category id from the resource id
         *
         * @param object $db
         * @param int $id
         * @return int cat_id
         */
        public static function getCategoryIdByResourceId($db, $id)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the resource Id
            $select->from('resource', 'cat_id')
            	   ->where('rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }//getCategoryIdByResourceId

        /**
         * Gets the start date from the resource id
         *
         * @param object $db
         * @param int $id
         * @return date start_date
         */
        public static function getStartDateByResourceId($db, $id)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the resource Id
            $select->from('resource', 'start_date')
            	   ->where('rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }

        /**
         * Gets the user_id from the resource id
         *
         * @param object $db
         * @param int $id
         * @return int user_id
         */
        public static function getUserIdByResourceId($db, $id)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the resource Id
            $select->from('resource', 'user_id')
            	   ->where('rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }

        /**
         * Gets the resource type id from the resource id
         *
         * @param object $db
         * @param int $rsrc_id
         */
        public static function getResourceTypeIdByResourceId($db, $id)
        {
            // instantiate a Zend select object
            $select = $db->select();
            
            // now get the resource Id
            $select->from('category',array())
            	   ->join('resource','resource.cat_id = category.cat_id', array('category.rsrc_type_id'))
            	   ->where('resource.rsrc_id = ?', $id);

            
            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }

        /**
         * Gets the resource type name from the resource id
         *
         * @param object $db
         * @param int $rsrc_id
         */
        public static function getResourceTypeNameByResourceId($db, $id = null)
        {
            if (!$id) {
            	return 'everything';
            }
        	
        	// instantiate a Zend select object
            $select = $db->select();
            $select->reset();
            
            // now get the resource Id
            $select->from(array('rt' => 'resource_type'),
            					array('rt.rsrc_type')
            			  )
            	   ->where('rt.rsrc_type_id = ?', $id);
            
            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }

        /**
         * Get the resources author (user name)
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getUserNameByResourceId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array()
							)
            	   ->join(array('u' => 'user'),
            			  'r.user_id = u.user_id',
            			   		array('user_name')
            			 );

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        } // getUserNameByResourceId

        /**
         * Get the resources text only
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getResourceTextByResourceId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('r.descrip')
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchRow($select);
        } // getResourceTextByResourceId

        /**
         * Get the resources title only
         *
         * @param db object $db
         * @param array $options
         * @return string
         */
        public static function getResourceTitleByResourceId($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('r.title')
							);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchRow($select);
        } // getResourceTitleByResourceId

        /**
         * Check if the resource is Active
         *
         * @param db object $db
         * @param array $options
         * @return bool
         */
        public static function getActiveStatus($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('r.is_active')
							);

            //Zend_Debug::dump($select->__toString());die;
            $result = (int) $db->fetchOne($select);
            
            return $result;
            
        } // getResourceTitleByResourceId

        /**
         * Check if the resource is owned
         * by the user_id
         *
         * @param db object $db
         * @param array $options
         * @return bool
         */
        public static function isOwner($db, $options)
        {
            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array('r.user_id')
							);

            //Zend_Debug::dump($select->__toString());die;
            if($db->fetchOne($select)) {
            	return true;
            }
            
            return false;
            
        } // getResourceTitleByResourceId

		/**
		 * Returns the distance in miles for
		 * the user's profile setting of
		 * short, medium or long as it applies to
		 * the event calendar on the dashboard
		 * 
		 * 
		 *
		 * @param string $userDistance
		 * @return int distance in miles
		 */
        public static function getDistance($userDistance = 'short', $userUnit = 'mi')
		{
			switch ($userDistance)
			{
				// 90 minute drive
				case 'short':
					$userUnit == 'mi' ? $distance = 80 : $distance = 128.7;
					break;
					
				// 3 to 4 hour drive
				case 'medium':
					$userUnit == 'mi' ? $distance = 212 : $distance = 341.1;
					break;
					
				// 5 hour drive
				case 'long':
					$userUnit == 'mi' ? $distance = 297 : $distance = 477.9;
					break;
					
				default:
					$userUnit == 'mi' ? $distance = 80 : $distance = 128.7;
					break;
					
			}
			
			return $distance;
		}
        
        /**
         * Gets a list of resoure ids
         * for a show code
         * (i.e. 'HMJ', 'YTK')
         *
         * @param object $db
         * @param array $options
         * @return array of rsrc_ids
         */
		public static function getResourcesByShowCode($db, $options)
        {
            // instantiate a Zend select object
            $select = $db->select();
            $select->reset();
            
            // now get the resource Id
            $select->from(array('re' => 'resource_extended'),
            					array('re.rsrc_id')
            			  )
	            	   ->join(array('r' => 'resource'),
	            			  're.rsrc_id = r.rsrc_id',
	            			   		array()
	            			 )
            			  ->where('re.extended_key = ?', 'show_code'
            	           )
            			  ->where('r.rsrc_date <= ?', date('Y-m-d H:i:s')
            	           )
            	   ->where('re.extended_value = ?', $options['show_code']
            	           );

            if ($options['show_code'] == ShowController::SHOW_ILHC2012 ) {
                $select->order('r.rsrc_date DESC');
            }
            	           
            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchAll($select);
        	
        	return $result;
        }

        /**
         * Gets a list of resoure ids
         * for the latest Top Featured stories
         *
         * @param object $db
         * @param int number to return
         * @return array of rsrc_ids
         */
		public static function getTopFeaturesIds($db, $count = 5)
        {
            // instantiate a Zend select object
            $select = $db->select();
            $select->reset();
            
            // now get the resource Id
            $select->from(array('r' => 'resource'),
            					array('r.rsrc_id')
            			  )
            	   ->where('r.cat_id in (?)', array(1,2,3,4)
            	           )
            	   ->where('r.is_active = 1'
            	           )
            	   ->where('rsrc_date <= ?', date('Y-m-d', $_SERVER['REQUEST_TIME'])
            	           );
            	           
            // set the offset, limit, and ordering of results
        	$select->limit($count, 0);
            	
            $select->order('rsrc_date DESC');

            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchAll($select);
        	
        	return $result;
        }

        /**
         * Get one resource id
         * for a show id
         *
         * @param unknown_type $db
         * @param unknown_type $options
         * @return unknown
         */
        public static function getResourceByEpisode($db, $options)
        {
            // instantiate a Zend select object
            $select = $db->select();
            $select->reset();
            
            // now get the resource Id
            $select->from(array('re' => 'resource_extended'),
            					array('re.rsrc_id')
            			  )
            	   ->where('re.extended_key = ?', 'episode'
            	           )
            	   ->where('re.extended_value = ?', $options['episode']
            	           );
            	           
            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchAll($select);
        	
        	return $result;
        }

        /**
         * Get the extra info for the
         * current upcoming Frim Fram
         * (for the /frimfram page)
         *
         * @param object $db
         * @return string
         */
        public static function getExtraFramInfo($db)
        {
            // instantiate a Zend select object
            $select = $db->select();
            $select->reset();
            
            $select->from(array('re' => 'resource_extended'),
            					array('re.extended_value')
            			  )
            	   ->join(array('r' => 'resource'),
            			  're.rsrc_id = r.rsrc_id',
            			   		array()
            			 )
            	   ->where('re.extended_key = ?', 'extraFramInfo'
            	           )
            	   ->where('r.start_date >= ?', date('Y-m-d')
            	           )
            	   ->order('r.start_date ASC')
            	   ->limit('1');
            	           
            //Zend_Debug::dump($select->__toString());die;
        	$result = $db->fetchOne($select);
        	
        	return $result;
        }

        /**
		 * update the resource table with
		 * the current number of comments
		 * For sorting on the discussion page
		 *
		 * @param database object $db
		 * @param array $options
		 * @return bool
		 */
        public static function setCountComments($db, $options){
			// Set the current post number in the resource db
			
        	if ($db->update('resource', array('count_comments' => $options['count_comments']),
        			 sprintf('%s = %d', 'rsrc_id', $options['rsrc_id']) )) {
        			 	
        		return true;
        	}
        	
        	return false;
	
		}
        
		/**
		 * update the resource table with
		 * the last comment Id
		 *
		 * @param database object $db
		 * @param array $options
		 * @return bool
		 */
        public static function setLastCommentId($db, $options){
			// Set the current post number in the resource db
			
        	if ($db->update('resource', array('last_comment_id' => $options['last_comment_id']),
        			 sprintf('%s = %d', 'rsrc_id', $options['rsrc_id']) )) {
        			 	
        		return true;
        	}
        	
        	return false;
	
		}
        
		/**
		 * update the resource table with
		 * the closed status
		 * Moderators only
		 *
		 * @param database object $db
		 * @param array $options
		 * @return bool
		 */
        public static function setClosedStatus($db, $options){
			
        	if ($db->update('resource', array('closed' => $options['status']),
        			 sprintf('%s = %d', 'rsrc_id', $options['rsrc_id']) )) {
        			 	
        		return true;
        	}
        	
        	return false;
	
		}
		
		/**
		 * update the resource table with
		 * the date_last_active
		 *
		 * @param database object $db
		 * @param array $options
		 * @return bool
		 */
        public static function setDateLastActive($db, $options){
			// Set the current post number in the resource db
			
        	if ($db->update('resource', array('date_last_active' => $options['date_last_active']),
        			 sprintf('%s = %d', 'rsrc_id', $options['rsrc_id']) )) {
        			 	
        		return true;
        	}
        	
        	return false;
	
		}

		/**
         * Attempts to load a resource by a given userId
         * or if the user is a moderator
         *
         * @param object $identity
         * @param int $post_id
         * @return resource object
         */
        public function loadForUser($identity, $rsrc_id)
        {
            $rsrc_id = (int) $rsrc_id;
            $user_id = (int) $identity->user_id;

            if ($rsrc_id <= 0 || $user_id <= 0)
                return false;

            // Is the user a moderator?
            if($identity->mod) {
	            $query = sprintf(
	                'select %s from %s where rsrc_id = %d',
	                join(', ', $this->getSelectFields()),
	                $this->_table,
	                $rsrc_id
	            );
            } else {
	            $query = sprintf(
	                'select %s from %s where user_id = %d and rsrc_id = %d',
	                join(', ', $this->getSelectFields()),
	                $this->_table,
	                $user_id,
	                $rsrc_id
	            );
            }

            return $this->_load($query);
        }

        /**
         * Updates a resource to a LIVE status
         *
         */
        public function sendLive() {
        	if ($this->is_active != self::STATUS_LIVE ) {
        		$this->is_active = self::STATUS_LIVE ;
        	}
        }
        
        /**
         * Updates a resource to an INACTIVE status (Mods)
         *
         */
        public function sendInactive() {
        	if ($this->is_active != self::STATUS_INACTIVE  ) {
        		$this->is_active = self::STATUS_INACTIVE  ;
        	}
        }
        
		/**
		 * Updates a resource to a DRAFT status
		 *
		 */
        public function sendBackToDraft()
		{
        	if ($this->is_active != self::STATUS_DRAFT ) {
        		$this->is_active = self::STATUS_DRAFT ;
        	}
		}
        
        /**
         * Checks the current LIVE / DRAFT status of a resource
         *
         * @return bool
         */
		public function isLive() {
        	return $this->isSaved() && $this->is_active == self::STATUS_LIVE ;
        }
        
        public function __set($name, $value)
        {
            switch ($name) {
                case 'latitude':
                case 'longitude':
                    $value = sprintf('%01.6lf', $value);
                    break;
            }

            return parent::__set($name, $value);
        }
        
		/**
		 * Formats portion of the WHERE clause for a SQL statement.
		 * SELECTs points within the $distance radius
		 *
		 * @param float $lat Decimal latitude
		 * @param float $lon Decimal longitude
		 * @param float $distance Distance in kilometers
		 * @return string
		 */
        public static function mysqlHaversine($lat = 0, $lon = 0, $distance = 0, $format = 'miles')
        {
			// This is how you might use it in a PHP script:
			//
			// $sqlQuery = 'SELECT field1 FROM Table1 WHERE '.mysqlHaversine(23.1344, -82.3713, 10);
			// $result = mysqli_query($link, $sqlQuery);
        	$miles = 3959;
        	$kilometers = 6372.797;
        	
        	($format == 'miles') ? $unit = $miles : $unit = $kilometers;
			
			//Zend_Debug::dump($distance);die;
			if ($distance > 0)
			{
				return ('
				  ((' . $unit . ' * (2 *
				    ATAN2(
				      SQRT(
				        SIN(('.($lat*1).' * (PI()/180)-l.latitude*(PI()/180))/2) *
				        SIN(('.($lat*1).' * (PI()/180)-l.latitude*(PI()/180))/2) +
				        COS(l.latitude * (PI()/180)) *
				        COS('.($lat*1).' * (PI()/180)) *
				        SIN(('.($lon*1).' * (PI()/180)-l.longitude*(PI()/180))/2) *
				        SIN(('.($lon*1).' * (PI()/180)-l.longitude*(PI()/180))/2)
				      ),
				      SQRT(1-(
				        SIN(('.($lat*1).' * (PI()/180)-l.latitude*(PI()/180))/2) *
				        SIN(('.($lat*1).' * (PI()/180)-l.latitude*(PI()/180))/2) +
				        COS(l.latitude * (PI()/180)) *
				        COS('.($lat*1).' * (PI()/180)) *
				        SIN(('.($lon*1).' * (PI()/180)-l.longitude*(PI()/180))/2) *
				        SIN(('.($lon*1).' * (PI()/180)-l.longitude*(PI()/180))/2)
				      ))
				    )
				  )) <= '.($distance*1). ')');
			}//if

			return '';
		}

		public static function clearResourceCache()
		{
            $memcache = new Memcache;
		    $memcache->connect("localhost",11211);
		    
		    $memcache->increment("cache_key");
		}

		public static function getResourceCacheId()
		{
            $memcache = new Memcache;
		    $memcache->connect("localhost",11211);

		    $myKey = $memcache->get("cache_key");
	        if($myKey===false) {
	           $memcache->set("cache_key", rand(1, 10000), 0);
	        }

		    return Zend_Registry::get('serverConfig')->cache_header . $myKey;
		}
		
		/**
		 * Adds a view to the db for a resource
		 * Cookie based. A view is good for 1 day.
		 *
		 * @param int $rsrc_id
		 * @param unknown_type $duration
		 */
        public static function addView($db, $rsrc_id, $duration = 86400)
		{
			$cookieName = Zend_Registry::get('serverConfig')->rsrcViewCookie;

			// check for the cookie
			$cookieExists = strpos(urldecode($_COOKIE[$cookieName]),(string)$rsrc_id);
			
			if ($cookieExists === false) {
				// update the db counter
				$db->query("UPDATE resource SET views_lifetime = views_lifetime + 1 WHERE rsrc_id = ?", $rsrc_id);				
				// create the cookie
				$cookieId = (isset($_COOKIE['yv'])) ? $_COOKIE['yv'].",".$rsrc_id : $rsrc_id;
				setcookie($cookieName, $cookieId, time()+$duration, '/', '.yehoodi.com'); // expires in 1 day
			}
			
		}

		/**
         * Gets view count for a resource 
         *
         * @param db object $db
         * @param array of $options
         * @return int
         */
        public static function getViewsLifetime($db, $options)
        {
        	$defaults = array(
        		'offset'	=> 0,
        		'limit'		=> 0,
        		'order'		=> 'order'
        	);
        	
            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = self::_getBaseQuery($db, $options);
            $select->from(null, 
            				array(  'r.views_lifetime'
								)
        				);

            //Zend_Debug::dump($select->__toString());die;
            return (int) $db->fetchOne($select);
        }//getCategoryByResourceTypeId()
        

	
    }