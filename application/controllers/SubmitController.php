<?php

/**
 * Yehoodi 3.0 SubmitController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Submit Form
 *
 */
class SubmitController extends CustomControllerAction 
{
	public $select_resource;
	public $select_category;
	
	public $rsrcId;
	public $select_resourceTypeId;
	public $select_categoryTypeId;

	public function init()
	{
        parent::init();
        
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Submit a new topic', $this->getUrl( null, 'submit'));
        
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // Get the page params
    	$this->request = $this->_request->getParam('action');

    	// get the list of resources
		$this->select_resource = DatabaseObject_ResourceType::getAllResourceTypes($this->db);
		
		// Only show type 'featured' and 'admin' if the current user is a mod
		if( !$this->identity->mod ) {
			unset($this->select_resource['featured']);
			unset($this->select_resource['admin']);
		}

		// this checks if we are on an iPad / iPod / iPhone
        $this->view->smart_device = $this->is_smart_device();
	} // init

	public function indexAction()
    {
		// Set up new date time
		$dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
		$this->dateTime = $dateTime->format("l F j, Y");
        
		// Get the page params
		$params = $this->_request->getParams();

		// Get the paramaters from the request url
		$request = $this->getRequest();

		// Create hidden_token if empty
        if (is_null($request->getPost('hidden_token'))) {
    		$hidden_token = $this->generateToken();
		}

    	// Is this a post?
    	if($this->_request->isPost()) {
    		// do post stuff

    		// Get our hidden_token
	    	$hidden_token = Zend_Filter::filterStatic($this->getRequest()->getPost('hidden_token'), 'StripTags');
    		
    		// Check Token for form validity
	    	if(!$this->tokenCheck($hidden_token)) {
	            // The token is INVALID. Log this error
	            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
		    		$message = sprintf('Invalid token on submit page from %s user %s.\nTitle: %s -- Descrip %s',
		                               $_SERVER['REMOTE_ADDR'],
		                               $this->identity->user_name,
		                               $request->getPost('title'),
		                               $request->getPost('inputDescription'));
		
		            $logger = Zend_Registry::get('errorLogger');
		            $logger->notice($message);
	            }
		        // send error to the next screen
				$this->messenger = $this->_helper->_flashMessenger;
	            $this->messenger->addMessage(array('error' => array('There was an error adding your post.')));
  	            
	            // redirect
	            // TODO: this never redirect. Figure it out...
	            $this->_redirect($this->getUrl('discussion','index'), array('exit'));
	    	}

	    	// Get the cat id from the post
	    	$this->select_categoryTypeId = (int) $request->getPost('cat_id');
	    	$select_resourceTypeId = DatabaseObject_Resource::getResourceTypeIdByCategoryId($this->db, $this->select_categoryTypeId);
			
	    	// Get the resource id?
	    	$this->rsrcId = (int) $params['id'];
	    	
	    	// Cancel Button
			if ($request->getPost('Cancel') == "Cancel") {
	    		$this->_redirect($this->getUrl(null,'discussion'));
	    		// Left the page...
			} 
			// Preview Image Button
			elseif ($request->getPost('prevImage') == "Upload") {
		    	
				// instantiate a new Resource object
		    	$fp = new FormProcessor_Resource($this->db, $this->identity->user_id, $this->rsrcId, $request);

		    	// instantiate a new ResourceImage object
	    		// this uploads to a temp dir for the photo and thumb
				$imgPreviewObj = new FormProcessor_ResourceImagePreview();
				
				// Upload the image
				if ($imgPreviewObj->process($request)) {
					$this->messenger = $this->_helper->_flashMessenger;
					$this->messenger->addMessage(array('notify' => array('Image Uploaded')));
	    		} else {
					// Image error
	    			foreach ($imgPreviewObj->getErrors() as $error){
						//$this->messenger->addMessage(array('error' => array($error)));
						$this->view->imageError = array($error);
					}
				}
				
				// Add the $imgPreviewObj to the session
				$imagePreview = new Zend_Session_Namespace('submitPreview');
				$imagePreview->filename 	= $imgPreviewObj->filename;
				$imagePreview->tempFilename = $imgPreviewObj->tempFilename;

				// RENDER PAGE
			}
			// Delete Image Button
			elseif ($request->getPost('deleteImage') == "Remove Image") {
				// instantiate a new Resource object
		    	$fp = new FormProcessor_Resource($this->db, $this->identity->user_id, $this->rsrcId, $request);

				$image_id = (int) $request->getPost('image');
				
				if($image_id > 0) {
					$resourceImage = new DatabaseObject_ResourceImage($this->db);
					if($resourceImage->loadForResource($this->rsrcId, $image_id)) {
						$resourceImage->delete();
						
						// reload $fp to get rid of the image->getId();
						$fp = new FormProcessor_Resource($this->db, $this->identity->user_id, $this->rsrcId, $request);
						$this->messenger = $this->_helper->_flashMessenger;
						$this->messenger->addMessage(array('notify' => array('Image Deleted')));
					}
				}

				// Delete from the session
				$this->messenger = $this->_helper->_flashMessenger;
				$this->messenger->addMessage(array('notify' => array('Image Deleted')));
				Zend_Session::namespaceUnset('submitPreview');
				// RENDER PAGE
			}
		    // Submit form
			elseif ($request->getPost('Submit')) {

				// instantiate a new Resource object
		    	$fp = new FormProcessor_Resource($this->db, $this->identity->user_id, $this->rsrcId, $request);

		    	// check if the request camefrom XMLHttpRequest (ajax) or not
		        $validate = $request->isXmlHttpRequest();

		        if ($validate) {
		    		// Only validate via Ajax
		            $fp->validateOnly(true);
		            $fp->process($request);
		        }
		        // Ajax call is valid or we aren't using Ajax
		        else if ($fp->process($request)) {
		        	// set the messenger
					$this->messenger = $this->_helper->_flashMessenger;

					if($request->getPost('Submit') == "Save Draft") {
						$draftLink = '/account/summary/drafts';
						$message = "Your topic was saved here in your account for later editing.";
						$this->messenger->addMessage(array('notify' => array($message)));
			    		
                        header('Location: ' . $this->getUrl('summary','account') . '/drafts');
						//$this->_redirect($this->getUrl('summary','account') . '/drafts');
					} else {
						$message = 'Your new topic has been posted!';
						$this->messenger->addMessage(array('notify' => array($message)));
			    		
						header('Location: ' . $this->getUrl(null,'comment') . $fp->resource->getId() . '/' . DatabaseObject_Resource::getResourceUrl($this->db, $fp->resource->getId()));
						//$this->_redirect($this->getUrl(null,'comment') . $fp->resource->getId() . '/' . DatabaseObject_Resource::getResourceUrl($this->db, $fp->resource->getId()));
					}
		        	
					// Redirect to discussion all view to see your new post
					// TODO: redirect to the SPECIFIC location of the posted topic
		    		//$this->_redirect($this->getUrl(null,'comment') . $fp->resource->getId() . '/' . DatabaseObject_Resource::getResourceUrl($this->db, $fp->resource->getId()));
		    		// Left the page...
				}
			}
    	}
    	// NOT FROM A FORM POST!!
    	else {
			// Remove image and map sessions
			Zend_Session::namespaceUnset('submitPreview');
			Zend_Session::namespaceUnset('mapSession');

    		// Resource Id provided: Edit a previous resource
    		if ($params['id'] > 0) {
    			//echo "Resource ID";
				// Set the category from edited resource
				$this->rsrcId = (int) $this->_request->getParam('id');
				
				// check if the rsrcId exsists and if the user owns it
				$rsrc = new DatabaseObject_Resource($this->db);
				
				if(!$rsrc->loadForUser($this->identity, $this->rsrcId) ) {
		        	$this->_redirect($this->getUrl(null,'discussion'));
				}

				// add the rsrc_id to the sql options
				$options = array('rsrc_id' => $this->rsrcId);
				
				$this->select_categoryTypeId = DatabaseObject_Resource::getCategoryIdByResourceId($this->db, $options );
				$this->hidden_resourceValue = DatabaseObject_Resource::getResourceNameByCategoryId($this->db, $this->select_categoryTypeId);
				$this->freqValue = DatabaseObject_Resource::getRepetitionById($this->db, $this->rsrcId);
    	    	$select_resourceTypeId = DatabaseObject_Resource::getResourceTypeIdByCategoryId($this->db, $this->select_categoryTypeId);
    	    	
    	    	// Copy the locations into the session
    	    	$this->copyLocationsToSession($this->rsrcId);
			// RENDER PAGE
    		}
    		// Category Id provided: new resource with a default category
    		elseif (!empty($params['cid'])) {
    			//echo "Cid";
    			// Set the category from the cid
				$this->select_categoryTypeId = (int) $request->getParam('cid');
				$this->hidden_resourceValue = DatabaseObject_Resource::getResourceNameByCategoryId($this->db, $this->select_categoryTypeId);
				$this->freqValue = "option_repetitionNone";
		    	$select_resourceTypeId = DatabaseObject_Resource::getResourceTypeIdByCategoryId($this->db, $this->select_categoryTypeId);
				// RENDER PAGE
    		}
    		// Blank url: Fresh resource
    		else {
				// Set the default category from the config.ini
    			$this->select_categoryTypeId = Zend_Registry::get('userConfig')->DefaultSubmitCategory;
				$this->hidden_resourceValue = DatabaseObject_Resource::getResourceNameByCategoryId($this->db, $this->select_categoryTypeId);
				$this->freqValue = "option_repetitionNone";
		    	$select_resourceTypeId = DatabaseObject_Resource::getResourceTypeIdByCategoryId($this->db, $this->select_categoryTypeId);
				// RENDER PAGE
    		}

    		// instantiate a new Resource object
	    	$fp = new FormProcessor_Resource($this->db, $this->identity->user_id, $this->rsrcId, $request);
    	}
    	
    	// RENDER PAGE
        
    	// set the default options for getting the list of categories for the template
        $categoryTypeOptions = array(
            'order'			=> 'order', 	// What are we ordering this result set by?
            'rsrc_type_id'	=> $select_resourceTypeId
        );

    	$this->select_category = DatabaseObject_Category::getCategoryByResourceTypeId($this->db, $categoryTypeOptions);

		if (!empty($validate)) {
			if ($fp->getErrors()) {
				$json = array(
						'errors' => $fp->getErrors()
						);
			} else {
				//user's submission validates, allow the user to register
				$json = array();
			}
			$this->sendJson($json);
	    } else {
	
	    	// Assign to Smarty
	    	$this->view->date = $this->dateTime;
	    	$this->view->hidden_token = $hidden_token;
			$this->view->fp = $fp;
			$this->view->defaultAvatar = Zend_Registry::get('userConfig')->DefaultAvatar;
			
			// I need a category Id for the template
			if ($fp->cat_id == 0)
				$this->view->fp->cat_id = $this->select_categoryTypeId;
				
		    $imagePreview = new Zend_Session_Namespace('submitPreview');
		    if((!empty($imagePreview->tempFilename))) {
				$this->view->preview = $imagePreview->tempFilename;
		    }
				
			$this->view->rsrc_type_id = $select_resourceTypeId;
			$this->view->cat_id = $this->select_categoryTypeId;
			$this->view->resourceTypes = $this->select_resource;
			$this->view->categoryTypes = $this->select_category;

		    // send messages to the user
	    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();

	    	// Set the default hidden_resourceValue for the hidden resource name that sets up the visible fields
			if ($this->hidden_resourceValue) {
				$this->view->hidden_resourceValue = $this->hidden_resourceValue;
			} else {
				$this->view->hidden_resourceValue = $request->getPost('hidden_resourceValue');
			}
			// Set the default freqValue for the hidden_fequencyValue that sets up the visible event fields
			if ($this->freqValue) {
				$this->view->freqValue = $this->freqValue;
			} else {
				$this->view->freqValue = $request->getPost('freqValue');
			}

			// Render!
			$this->_helper->viewRenderer('index');
	    }
    }
    
    /**
     * Copies the locations for a resource from the
     * database to the session for editing on the 
     * submit page.
     * 
     * After this is done, the form processor takes
     * care of deleting the old locations and adding
     * the new
     *
     * @param int $rsrcId
     */
    protected function copyLocationsToSession($rsrcId)
    {
		// get the locations
		$locations = DatabaseObject_Location::getLocations($this->db, array('rsrc_id' => $rsrcId));
		
		// create the session
		$map = new SubmitMapSessionHandler();
		
		// Iterate and add to session
		foreach($locations as $value) {
			$map->setLongitude($value['longitude']);
			$map->setLatitude($value['latitude']);
			$map->setDescription($value['description']);
			$map->setPrimary($value['primary_location']);
			$map->setStreetAddress($value['street_address']);
			$map->setCity($value['city']);
			$map->setState($value['state']);
			$map->setZip($value['zip']);
			$map->setCountry($value['country']);
			
			$map->addLocation();
		}
		
		// add to active session
		$values = new Zend_Session_Namespace('mapSession');
    }


	public function locationsAction()
	{
		$request = $this->getRequest();
		
		$rsrc_id = (int) $request->getQuery('id');
		
		$this->view->resource = $resource;
	}
}