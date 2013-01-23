<?php

/**
 * Yehoodi 3.0 SubmitAjaxController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class SubmitajaxController extends CustomControllerAction 
{
	public $rsrcId;
	protected $ajaxPass;

	const RELATED_TITLE_MATCH_LIMIT =20;

	public function init()
	{
        parent::init();
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

		// Set up Sphinx Search Client
		$sphinxIP = Zend_Registry::get('searchConfig')->sphinxLocation;
		$sphinxPort = (int) Zend_Registry::get('searchConfig')->sphinxPort;
		
        $this->sphinxClient = new SphinxClient();
        $this->sphinxClient->SetServer( $sphinxIP, $sphinxPort);
        $this->sphinxClient->SetConnectTimeout( 30 );
        
        
        $this->sphinxClient->SetFieldWeights(array('title' => 1000, 'description' => 10));    // Weights for field search
        $this->sphinxClient->SetLimits( 0, self::RELATED_TITLE_MATCH_LIMIT, self::RELATED_TITLE_MATCH_LIMIT ); // How many records to pull
        $this->sphinxClient->SetSortMode( SPH_SORT_ATTR_DESC, 'date' );  // Sort with most recent stuff first. Cool!
        $this->sphinxClient->SetArrayResult( true );    // Give me an array when done.

	} // init


	public function locationsAction()
	{
		if($this->identity && $this->_request->isXmlHttpRequest()) {
			$request = $this->getRequest();
			
			$rsrc_id = (int) $request->getQuery('id');
			
	/*		$resource = new DatabaseObject_Resource($this->db);
			if (!$resource->loadForUser($this->identity->user_id, $rsrc_id)) {
				$this->_redirect($this->getUrl());
			}
	*/
			$this->view->resource = $resource;
		} else {
			die("Access Denied");
		}
	}

	/**
     * Refresh the submit page category list
     * with the appropriate categories based on
     * the chosen resource type id via ajax
	 *
	 * @param smarty template $tpl
	 * @param int $select_resourceTypeId
	 * @return html
	 */
    public function ajaxselectcategoryAction()
    {
		if($this->identity && $this->_request->isXmlHttpRequest()) {

			$templater = new Templater();
			$tpl = 'ajax-submit-category-list.tpl';
			$select_resourceTypeId = $this->_request->getParam('select_resourceTypeId');
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	        // select options
	        $options = array('rsrc_type_id' => (int) $select_resourceTypeId
	        			);
	        
	        $templater->categoryTypes = DatabaseObject_Category::getCategories($this->db, $options);
	
	        // fetch the category list output
	        $output = $templater->render('lib/' . $tpl);
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
    }

	/**
     * Checks for exact or similar Resource
     * titles on the submit page to 
     * prevent users from entering
     * duplicate titles.  
     * 
	 * @param smarty template $tpl
	 * @param int $select_resourceTypeId
	 * @return html
	 */
    public function ajaxtitlematchAction($title = '')
    {
		if($this->identity && $this->_request->isXmlHttpRequest()) {

			$title = trim($this->_request->getParam('title'));
	
			if($title == '') {
	        	return;
	        }
	    	
	    	$templater = new Templater();
			$tpl = 'ajax-title-match.tpl';
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        

        	$type = "resources";
			$limit = self::RELATED_TITLE_MATCH_LIMIT ;
            
			//$this->sphinxClient->setFilter('rsrc_type_id', array(3));	
            
			$searchQuery = $title;
            $searchResults = $this->sphinxClient->Query( $searchQuery, $type );
			
            // Did we get any resources that met the minimum score?
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
			    
				//Zend_Debug::dump($resourceOrder);
				if (count($resourceIds)) {
    			    // Build the list of resources from the array
    				$options = array('rsrc_id' => $resourceIds);
    				
    				// Get the resources from the db
    			    $resources = DatabaseObject_Resource::getResourceById($this->db, $options);
    			    
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
    
    			    // Add them to the templater object
    			    $templater->resources = $resources;
    		
    		        // output the matched resources
    		        $output = $templater->render('lib/' . $tpl);
    		        
    		        echo $output;
				}
			} else {
				return;
			}
		} else {
			die("Access Denied");
		}
    }

    /**
     * Manages the Ajax calls to the 
     * Google Map
     *
     */
    public function locationsmanageAction() {
    	
    	$request = $this->getRequest();
    	
    	$action = $request->getPost('action');
    	$rsrc_id = $request->getPost('rsrc_id');
    	
    	$ret = array('rsrc_id'	=>	0);
    	
    	$resource = new DatabaseObject_Resource($this->db);
    	
        switch ($action) {
            case 'get':
                $ret['locations'] = array();

	    		$values = new Zend_Session_Namespace('mapSession');
                if ($values->location) {
                    foreach ($values->location as $location) {
	                        $ret['locations'][] = array(
	                            'location_id' 		=> $location['location_id'],
	                            'latitude'    		=> $location['latitude'],
	                            'longitude'   		=> $location['longitude'],
	                            'description' 		=> $location['description'],
	                            'street_address' 	=> $location['street_address'],
	                            'city' 				=> $location['city'],
	                            'state' 			=> $location['state'],
	                            'zip' 				=> $location['zip'],
	                            'country' 			=> $location['country'],
	                            'primary_location' 	=> $location['primary_location']
	                        );
                    }
            	}
	    		break;

    		case 'add':
	    		// SubmitMapSessionHandler create
				$location = new SubmitMapSessionHandler();
	    		
				$location->setLongitude($request->getPost('longitude'));
				$location->setLatitude($request->getPost('latitude'));
				$location->setDescription($request->getPost('description'));
				$location->setStreetAddress($request->getPost('street_address'));
				$location->setCity($request->getPost('city'));
				$location->setState($request->getPost('state'));
				$location->setZip($request->getPost('zip'));
				$location->setCountry($request->getPost('country'));
		
				$location->addLocation();
	    		
	    		$location_id = $location->lastId();
	    		
	    		$values = new Zend_Session_Namespace('mapSession');

	    		$ret['location_id'] 		= $location_id;
	    		$ret['rsrc_id'] 			= 1;
	    		$ret['latitude'] 			= $values->location[$location_id]['latitude'];
	    		$ret['longitude'] 			= $values->location[$location_id]['longitude'];
	    		$ret['description'] 		= $values->location[$location_id]['description'];
	    		$ret['street_address'] 		= $values->location[$location_id]['street_address'];
	    		$ret['city'] 				= $values->location[$location_id]['city'];
	    		$ret['state'] 				= $values->location[$location_id]['state'];
	    		$ret['zip'] 				= $values->location[$location_id]['zip'];
	    		$ret['country'] 			= $values->location[$location_id]['country'];
	    		$ret['primary_location'] 	= $values->location[$location_id]['primary_location'];
	    		break;
	    		
            case 'delete':
	    		// SubmitMapSessionHandler create
				$location = new SubmitMapSessionHandler();

            	$location_id = $request->getPost('location_id');
				$ret['location_id'] = $location_id;
            	
				$delete = $location->getLocation($location_id);
            	
            	if($delete['primary_location'] == 1) {
                    $location->deleteLocation($location_id);
                	$location->updatePrimaryLocation();
                } else {
                    $location->deleteLocation($location_id);
                }
                break;
		}
    	$this->sendJson($ret);
    }    
    
    /**
     * Diplays locations to the 
     * Google Map
     *
     */
    public function mapdisplayAction() {
    	
    	$request = $this->getRequest();
    	
    	$action = $request->getPost('action');
    	$rsrc_id = $request->getPost('rsrc_id');
    	
    	$ret = array('rsrc_id'	=>	0);
    	
    	$resource = new DatabaseObject_Resource($this->db);
    	
    	if ($rsrc_id > 0) {
	    	// From the database
    		if ($resource->load($rsrc_id)) {
	    		$ret['rsrc_id'] = $resource->getId();
	    		
	    		switch ($action) {
	                case 'get':
	                    $ret['locations'] = array();
	                    //Zend_Debug::dump($resource);die;
	                    foreach ($resource->locationsArray as $location) {
	                        $ret['locations'][] = array(
	                            'location_id' 		=> $location['location_id'],
	                            'latitude'    		=> $location['latitude'],
	                            'longitude'   		=> $location['longitude'],
	                            'description' 		=> $location['description'],
	                            'street_address' 	=> $location['street_address'],
	                            'city' 				=> $location['city'],
	                            'state' 			=> $location['state'],
	                            'zip' 				=> $location['zip'],
	                            'country' 			=> $location['country'],
	                            'primary_location' 	=> $location['primary_location']
	                        );
	                    }
	
	                    break;
            	}
	    	}
    	}
    	$this->sendJson($ret);
    }
}