<?php
	class SearchController extends CustomControllerAction 
	{
		protected $processUserSearch = false;
		
		// pagination vars
		protected $limit;
		protected $adjacents;
		protected $page;
		protected $offset;

		public function init()
		{
	        parent::init();
	        
	        // Get our user
            $this->identity = Zend_Auth::getInstance()->getIdentity();
            
	        // Assign our top level breadcrumb
	        $this->breadcrumbs->addStep('Search', $this->getUrl( null, 'search'));

	        // Get the page params
	        $this->page = (int) $this->_request->getParam('p');
	        if (!$this->page) {
	        	$this->page =1;
	        }

            // Get the request!
			$this->request = $this->getRequest();

            $this->limit = (int) Zend_Registry::get('paginationConfig')->ResourcesPerPage;
	    	$this->adjacents = Zend_Registry::get('paginationConfig')->Adjacents;
	        
    		//$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
            //$this->page = max(1, min($lastpage, $this->page));
	        $this->offset = (int) ($this->page - 1) * $this->limit;

            // Set up Sphinx Search Client
    		$this->maxSearchResults = Zend_Registry::get('searchConfig')->sphinxMaxReturns;
            $sphinxIP = Zend_Registry::get('searchConfig')->sphinxLocation;
    		$sphinxPort = (int) Zend_Registry::get('searchConfig')->sphinxPort;
    		
            $this->sphinxClient = new SphinxClient();
            $this->sphinxClient->SetServer( $sphinxIP, $sphinxPort);
            $this->sphinxClient->SetConnectTimeout( 30 );

            $this->sphinxClient->SetLimits( $this->offset, $this->limit, $this->maxSearchResults ); // How many records to pull
		}
		
		public function indexAction()
		{
			
        	// clear the vars
			$this->view->type = null;
			$this->view->user = null;
			$this->view->totalResources = null;
			$this->view->totalComments = null;
			$this->view->totalEvents = null;
			$this->view->totalUsers = null;
			$this->view->search = null;
			
            if (!$this->request->getParam('q') || 
					$this->request->getParam('q') == '' || 
					$this->request->getParam('q') == 'Search' || 
					strlen($this->request->getParam('q')) < 3) {
				// Ask user to enter a search term
			} else {
				$this->processQuerySearch();
			}
			
			// Render!
			$this->_helper->viewRenderer('index');
		}
		
		public function processQuerySearch()
		{
			// get the query
			$query = $this->request->getParam('q');
			$this->logSearch($query);

        	$searchQuery = str_replace(array('[bleep!]'),'',$query);
            $type = $this->request->getParam('type');
            $usersearch = $this->request->getParam('user');
        	
            if ($type) {
    				
            	$validTypes = array('resources',
            	                    'comments',
            	                    'events',
            	                    'users',
            	                    );
    
    			if (in_array($type, $validTypes)) {
    			    if ($usersearch == 'true') {
        				$searchQuery = UtilityController::cleanUpUserName($searchQuery);
               			//$user_id = DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $user));
    	       			$this->processUserSearch = true;
    			    }
    			}
			} 
    	
    		$type = $this->processTextInput($this->request->getParam('type'), FALSE, TRUE);
			$offset = $this->processTextInput($this->request->getParam('offset'), FALSE, TRUE);
			$sort = $this->processTextInput($this->request->getParam('sort'), FALSE, TRUE);
			
            // Set up search params
            				
			if ($this->processUserSearch) {
                $this->sphinxClient->SetMatchMode( SPH_MATCH_PHRASE );   // Mode set for exact matching the query

                // User - Specific user searches
                // Topics
    			$this->sphinxClient->ResetFilters(); 
                $this->sphinxClient->SetSortMode( SPH_SORT_TIME_SEGMENTS, 'date' );  // Sort with most recent stuff first. Cool!
               	$this->sphinxClient->AddQuery( $searchQuery, 'resource_usercounts' );

               	// Comments
    			$this->sphinxClient->ResetFilters(); 
                $this->sphinxClient->SetSortMode( SPH_SORT_TIME_SEGMENTS, 'date' );  // Sort with most recent stuff first. Cool!
               	$this->sphinxClient->AddQuery( $searchQuery, 'comment_usercounts' );

               	// Events
    			$this->sphinxClient->ResetFilters(); 
                $this->sphinxClient->SetSortMode( SPH_SORT_TIME_SEGMENTS, 'date' );  // Sort with most recent stuff first. Cool!
               	$this->sphinxClient->AddQuery( $searchQuery, 'event_usercounts' );
			} else {
                $this->sphinxClient->SetMatchMode( SPH_MATCH_EXTENDED2 );   // Mode set for matching the query
                $this->sphinxClient->SetRankingMode( SPH_RANK_PROXIMITY_BM25 );

                // Topics
    			$this->sphinxClient->ResetFilters(); 
    			
    			$this->sphinxClient->SetFieldWeights(array('title' => 90, 'description' => 10));    // Weights for field search
                $this->sphinxClient->SetSortMode( SPH_SORT_TIME_SEGMENTS, 'date' );  // Sort with most recent stuff first. Cool!
    			$this->sphinxClient->AddQuery( $searchQuery, 'resources' );
    
    			// Comments
    			$this->sphinxClient->ResetFilters(); 
                $this->sphinxClient->SetFieldWeights(array('comments' => 100));
    			$this->sphinxClient->AddQuery( $searchQuery, 'comments' );
    
    			// Events
    			$this->sphinxClient->ResetFilters(); 
                $this->sphinxClient->SetFieldWeights(array('title' => 50, 'description' => 50));
                $this->sphinxClient->SetSortMode( SPH_SORT_TIME_SEGMENTS, 'date' );  // Sort with most recent stuff first. Cool!
    			$this->sphinxClient->AddQuery( $searchQuery, 'events' );
    			
    			// Users
    			$this->sphinxClient->ResetFilters(); 
                $this->sphinxClient->SetFieldWeights(array('user_name' => 100));
               	$this->sphinxClient->AddQuery( $searchQuery, 'users' );
			}
            
			$this->sphinxClient->SetArrayResult( true );    // Give me an array when done.

            
            // Run Query
            $searchResults = $this->sphinxClient->RunQueries();
            
			// Get the query to an array for highlighting later
		    if (!empty($searchResults[0]['words'])) {
    			$words = array_keys($searchResults[0]['words']);
		    } else {
		        $words = array();
		    }
			
            //Zend_Debug::dump($searchResults);
            
            $searchGroup = array();
            //$groupCount = 0;
            
            foreach ($searchResults as $resultKey => $resultSet) {
                // Did we get any resources that met the minimum score?
    			if ($resultSet['total_found']) {
    				
    				$ids = array();
    				$resourceOrder = array();
    				$counter = 0;
    				if (!empty($resultSet['matches'])) {
        				foreach ($resultSet['matches'] as $match) {
        			        $ids[] = $match['id'];
        			        $resourceOrder[$match['id']] = $counter++;
        				}
    				}
    			    
    				if (count($ids)) {
        				// Get the resources from the db
        				if ($resultKey == 0) {
            				$options = array('rsrc_id' => $ids);
                            $resources = DatabaseObject_Resource::getResourceById($this->db, $options);
        				} elseif ($resultKey == 1) {
            				$options = array('comment_id' => $ids);
                            $resources = DatabaseObject_Comment::getComments($this->db, $options);
        				} elseif ($resultKey == 2) {
            				$options = array('rsrc_id' => $ids);
                            $resources = DatabaseObject_Resource::getResourceById($this->db, $options);
        				} elseif ($resultKey == 3) {
            				$options = array('user_id' => $ids);
                            $resources = DatabaseObject_User::GetUsers($this->db, $options);
        				}
        			    // Sort by order from sphinx
        			    
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
    				}

    				$searchGroup[] = $resources;
    			    
    			} else {
    			    $searchGroup[] = 0;
    			}
            }

            // First, get the counts for the tabs
	        $totalResources = ($searchResults[0]['total_found'] < $this->maxSearchResults) ? $searchResults[0]['total_found'] : $this->maxSearchResults ;
	        $totalComments = ($searchResults[1]['total_found'] < $this->maxSearchResults) ? $searchResults[1]['total_found'] : $this->maxSearchResults;
	        $totalEvents = ($searchResults[2]['total_found'] < $this->maxSearchResults) ? $searchResults[2]['total_found'] : $this->maxSearchResults;
	        $totalUsers = ($searchResults[3]['total_found'] < $this->maxSearchResults) ? $searchResults[3]['total_found'] : $this->maxSearchResults;
			
			//Zend_Debug::dump($searchGroup);die;

			switch($type) {
			    case 'resources':
    				$totalResults = $searchResults[0]['total_found'];
			        break;
			        
			    case 'comments':
    				$totalResults = $searchResults[1]['total_found'];
			        break;
			        
			    case 'events':
    				$totalResults = $searchResults[2]['total_found'];
			        break;
			        
			    case 'users':
    				$totalResults = $searchResults[3]['total_found'];
			        break;
			        
			    default:
			        $type = 'resources';
			        $totalResources = ($searchResults[0]['total_found'] < $this->maxSearchResults) ? $searchResults[0]['total_found'] : $this->maxSearchResults;
    				$totalResults = $searchResults[0]['total_found'];
			        break;
			        
			}
			
			if ($totalResults > $this->maxSearchResults) {
			    $totalResults = $this->maxSearchResults;
			}
			
			$search = array('performed' => TRUE, 'total' => $totalResults);
			
			$searchPaginationLink = "search/?q=".urlencode($query)."&amp;type={$type}";
		    if ($usersearch) {
		        $searchPaginationLink .= "&amp;user=true";
		    }
		    $searchPaginationLink .= "&amp;p=";
			
			// Assign to Smarty
            $this->view->q = $query;
            $this->view->words = $words;
            $this->view->search = $search;
            $this->view->user = $usersearch;

			$this->view->pageResultNum = $this->limit;
	        $this->view->totalResults = $totalResults;
	        $this->view->totalResources = $totalResources;
	        $this->view->totalComments = $totalComments;
	        $this->view->totalEvents = $totalEvents;
	        $this->view->totalUsers = $totalUsers;

	        $this->view->pageNumber = $this->page;
	        
	        // which resource are we paging through?
	        switch ($type) {
	        	
	        	case 'resources':
		        	$this->view->type = 'resources';
		            $this->view->resources = $searchGroup[0];
	        		break;

	        	case 'comments':
		        	$this->view->type = 'comments';
		            $this->view->comments = $searchGroup[1];
	        		break;
	        	
	        	case 'events':
		        	$this->view->type = 'events';
		            $this->view->resources = $searchGroup[2];
	        		break;
	        	
	        	case 'users':
		        	$this->view->type = 'users';
					$this->view->users = $searchGroup[3];
	        		break;
	        	
	        	default:
	        		$this->view->type = 'resources';
		            $this->view->resources = $searchGroup[0];
	        		break;
	        }

	        if ($totalResults > $this->limit) {
		        $this->getPaginationString($this->page, $totalResults, $this->limit, $this->adjacents, "/",$searchPaginationLink);
	        }
		}
		
		/**
		 * Process user input from a text field in a form
		 *
		 * @param string $text
		 * @param bool $allowHtml
		 * @param bool $alterQuotes
		 * @return string
		 */
		public static function processTextInput( $text, $allowHtml = FALSE, $alterQuotes = TRUE ) {
			$s = stripslashes(trim($text));
			if (!$allowHtml) {
				if ($alterQuotes) {
					$s = htmlspecialchars($s, ENT_QUOTES);
				} else {
					$s = htmlspecialchars($s, ENT_NOQUOTES);
				}
			}
			return $s;
		}
		
		/**
		 * Logs the user's search
		 *
		 */
		public function logSearch($query)
		{
            if($this->identity) {
                $user = $this->identity->user_name;
            } else {
                $user = 'anonymous';
            }
            
            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
			    $message = sprintf('Search query from user %s at IP: %s: %s',
	                               $user,
	                               $_SERVER['REMOTE_ADDR'],
	                               $query);
	
	            $logger = Zend_Registry::get('searchlogger');
	            $logger->notice($message);
            }
		}
}