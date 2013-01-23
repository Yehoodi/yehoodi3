<?php
    class FormProcessor_Location extends FormProcessor
    {
        protected $resource;
        public $location;

        public function __construct(DatabaseObject_Resource $resource)
        {
            parent::__construct();

            $this->resource = $resource;

            // set up the initial values for the new location
            $this->location = new DatabaseObject_Location($resource->getDb());
            $this->location->rsrc_id = $this->resource->getId();
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            $this->description 		= $this->sanitize($request->getPost('description'));
            $this->street_address 	= $this->sanitize($request->getPost('street_address'));
            $this->city 			= $this->sanitize($request->getPost('city'));
            $this->state 			= $this->sanitize($request->getPost('state'));
            $this->zip 				= $this->sanitize($request->getPost('zip'));
            $this->country 			= $this->sanitize($request->getPost('country'));
            $this->longitude   		= $request->getPost('longitude');
            $this->latitude    		= $request->getPost('latitude');

            // if no errors have occurred, save the location
            if (!$this->hasError()) {
                $this->location->description 		= $this->description;
                $this->location->street_address 	= $this->street_address;
                $this->location->city 				= $this->city;
                $this->location->state				= $this->state;
                $this->location->zip				= $this->zip;
                $this->location->country			= $this->country;
                $this->location->longitude   		= $this->longitude;
                $this->location->latitude    		= $this->latitude;
                
                $locationCount = DatabaseObject_Location::getLocationCount($this->resource->getDb(), $this->resource->getId());
                
                if ($locationCount) {
                	$this->location->primary_location = 0;
                } else {
                	$this->location->primary_location = 1;
                }
                $this->location->save();
            }

            return !$this->hasError();
        }
    }
?>