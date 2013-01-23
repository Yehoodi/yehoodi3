<?php
    class DatabaseObject_ResourceMeta
    {
        public $resourceName = null;
        public $categoryName = null;
        public $categoryUrl = null;
        public $numOfCommnets = null;
        public $postedBy = null;
        public $relativeDate = null;
        public $shortDate = null;
        public $cat_id = null;
        public $rsrc_id = null;
        public $resourceUrl;
        public $lastCommentUserName;
        public $lastCommentUrl;
        public $viewsLifetime;
        public $lastCommentDate;
        public $lastCommentRelativeDate;
        public $distance;
        
    	/**
    	 * Takes a resource id and uses protected
    	 * methods to set this objects
    	 * properties for all the meta information
    	 * for the resource
    	 *
    	 * @param object $db
    	 * @param object $resource
    	 */
        public function __construct($db, $resourceObj = null)
        {
            if (is_object($resourceObj)) {
                $this->_id = $resourceObj->getId();
                $this->lastCommentId = $resourceObj->last_comment_id;
                $this->user_id = $resourceObj->user_id;
                $this->cat_id = $resourceObj->cat_id;
                $this->rsrc_date = $resourceObj->rsrc_date;

	        	$this->_db = $db;
	        	
	        	$this->setResourceAndCategoryName();
	        	$this->setNumberOfComments();
	        	$this->setPostedBy();
	        	$this->setRelativeDate();
	        	$this->setShortDate();
	        	if ($this->setLastCommentUserName()) {
	        		$this->getResourceUrl();
	        	}
	        	$this->getViewsLifetime();
	        	$this->setLastCommentDate();
	        	$this->setLastCommentRelativeDate();
	        	$this->getDistance();
            }
        }

        /**
         * Sets the properties for Category name
         * and resource name
         *
         */
        protected function setResourceAndCategoryName()
        {
            // instantiate a Zend select object
            $select = $this->_db->select();
            
            // now get the category Name
            $select->from('category',
            				array('cat_type',
            					  'cat_id',
            					  'cat_site_url'
            				)
            			  )
            	   ->join('resource_type',
            	   		  'resource_type.rsrc_type_id = category.rsrc_type_id',
            	   		   array('rsrc_type',
            	   		   		 'rsrc_type_id')
            	   		   )
            	   ->where('cat_id = ?',
            	   		   $this->cat_id)
            	   		 ;
            
        	$result = $this->_db->fetchAll($select);
        	
        	$this->categoryName = $result[0]['cat_type'];
        	$this->categoryUrl = $result[0]['cat_site_url'];
        	$this->resourceName = $result[0]['rsrc_type'];
        }//setResourceAndCategoryName

        /**
         * the number of comments for the resource
         * from the DatabaseObject_Comment
         *
         */
        protected function setNumberOfComments()
        {
        	$result = DatabaseObject_Comment::getCommentCount($this->_db, array('rsrc_id' => $this->_id));
        	
        	$this->numOfCommnets = $result;
        }
        
        /**
         * Who posted this resource?
         *
         */
        protected function setPostedBy()
        {
            // instantiate a Zend select object
            $select = $this->_db->select();
            
            // First get the category id
            $select->from('user','user_name')
            	   ->where('user_id = ?', $this->user_id);
            
            $this->postedBy = $this->_db->fetchOne($select);
        }//setPostedBy
        
        /**
         * Sets the relative Date for the resource
         * Example: (8 days ago)
         *
         */
        public function setRelativeDate()
        {
            $this->relativeDate = common::getRelativeTime($this->rsrc_date);
        }//setRelativeDate
        
        /**
         * Sets the short Date for the resource
         * Example: 10/21/2009
         *
         */
        public function setShortDate()
        {
            $this->shortDate = common::shortDateTime($this->rsrc_date);
        }//setNeatDate
        
        /**
         * Sets the properties for last commented user
         *
         */
        protected function setLastCommentUserName()
        {
			// get the user_name
			if($this->lastCommentUserName = DatabaseObject_Comment::getUserNameByCommentId($this->_db, array('comment_id' => $this->lastCommentId))) {
				return $this->lastCommentUserName;
			} else {
				return false;
			}
        }
        
        protected function getResourceUrl() 
        {
            // get the number of comments per page from the config
            $commentsPerPage = Zend_Registry::get('paginationConfig')->CommentsPerPage;
        	
            // get the comment number
            $comment_num = DatabaseObject_Comment::getCommentNumberByCommentId($this->_db, array('comment_id' => $this->lastCommentId));
            
			// calculate the page number this comment is on
			if($comment_num < $commentsPerPage) {
				$commentPageNum =1;
			} else {
				$commentPageNum = ceil(DatabaseObject_Comment::getCommentCount($this->_db, array('rsrc_id' => $this->_id)) / $commentsPerPage);
			}

			// get the url from the static method in DatabaseObject_Resource
            $urlSEO = DatabaseObject_Resource::getResourceUrl($this->_db, $this->_id);
            
            $rootUrl = "{$this->_id}/{$urlSEO}/{$commentPageNum}#comment_{$comment_num}";

            $this->lastCommentUrl = $rootUrl;
        }
        
        protected function getViewsLifetime()
        {
        	$result = DatabaseObject_Resource::getViewsLifetime($this->_db, array('rsrc_id' => $this->_id));
        	
        	$this->viewsLifetime = $result;
        }
        
        /**
         * Sets the last comment Date for the
         * resource
         * 
         * Example: (2010-04-23 12:45:01)
         *
         */
        public function setLastCommentDate()
        {
            $lastComment = DatabaseObject_Comment::getLastCommentIdByResourceId($this->_db, array('rsrc_id' => $this->_id));
        	if (!$lastComment) {
        	    $this->lastCommentDate = $this->rsrc_date;
        	} else {
                $this->lastCommentDate = $lastComment;
        	}
        }

        /**
         * Sets the relative Date for the last
         * comment in the resource
         * 
         * Example: (2 minutes ago)
         *
         */
        public function setLastCommentRelativeDate()
        {
            //$lastComment = DatabaseObject_Comment::getLastCommentIdByResourceId($this->_db, array('rsrc_id' => $this->_id));
            $lastComment = $this->lastCommentDate;
        	$this->lastCommentRelativeDate = common::getRelativeTime($lastComment);
        }
        
        /**
         * Sets the
         * and resource name
         *
         */
        public function getDistance()
        {
        	// get the user's location settings
    		$auth = Zend_Auth::getInstance();
    		
    		if(!$auth) {
		        $userLat = $auth->getIdentity()->latitude;
		        $userLon = $auth->getIdentity()->longitude;
		        $userUnit = $auth->getIdentity()->unit;
	
		        if($this->resourceName == 'event') {
		
			        // instantiate a Zend select object
		            $select = $this->_db->select();
		            
		            // now get the category Name
		            $select->from('location',
		            				array('longitude',
		            					  'latitude'
		            				)
		            			  )
		            	   ->where('rsrc_id = ?',
		            	   		   $this->_id)
		            	   ->where('primary_location = ?',
		            	   		   1)
		            	   		 ;
		            
		            //Zend_Debug::dump($select->__toString());die;
		        	if($result = $this->_db->fetchAll($select)) {
		        	
			        	$lon = $result[0]['longitude'];
			        	$lat = $result[0]['latitude'];
			        	
				        $this->distance = UtilityController::getHaversineDistance($userLat, $userLon, $lat, $lon, $userUnit) . " {$userUnit}";
		        	} else {
		        		$this->distance = 'N/A';
		        	}
		        	//Zend_Debug::dump($this->distance);die;
	        	}
	        }
        }
     }