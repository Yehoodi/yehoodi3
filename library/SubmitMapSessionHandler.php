<?php
class SubmitMapSessionHandler
{
	const PRIMARY_LOCATION = 1;
	const OTHER_LOCATION = 0;
	
	const MAX_LOCATIONS = 5;
	
	public $session;
	
	public $long;
	public $lat;
	public $description;
	public $streetAddress;
	public $city;
	public $state;
	public $zip;
	public $country;
	public $primary = self::OTHER_LOCATION;

	protected $_id;
	public function __construct() {

		$this->session = new Zend_Session_Namespace('mapSession');
	}
	
	public function setLongitude($long)
	{
		$this->long = $long;
	}
	
	public function setLatitude($lat)
	{
		$this->lat = $lat;
	}
	
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function setStreetAddress($streetAddress)
	{
		$this->streetAddress = $streetAddress;
	}
	
	public function setCity($city)
	{
		$this->city = $city;
	}
	
	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function setZip($zip)
	{
		$this->zip = $zip;
	}
	
	public function setCountry($country)
	{
		$this->country = $country;
	}
	
	public function setPrimary($primary)
	{
		$this->primary = $primary;
	}
	
	public function lastId()
	{
		return $this->_id;
	}
	
	public function getLocation($location_id)
	{
		return $this->session->location[$location_id];
	}
	
	/**
	 * Adds a location to the mapSession session
	 *
	 * @param float $long
	 * @param float $lat
	 * @param string $description
	 * @param bool $primary
	 */
	public function addLocation()
	{
		$count = 0;
		
		$count = count($this->session->location);
		
		if ($count >= self::MAX_LOCATIONS ) {
			return "Marker limit reached.";
		}
		
		if (!$count) {
			$this->primary = self::PRIMARY_LOCATION;
		}
		
		$this->session->location[] = array();
								  		   
		$array_max = array_keys($this->session->location);
		$key_max = max($array_max);
		
		// Crazy hacking shit to force the array to start with index 1
		// instead of 0 since it triggers an error on the map
		// Databases don't keep id's of 0
		if ($key_max == 0) {
			$this->session->location = array_merge(array(''),$this->session->location);
			$key_max++;
			unset($this->session->location[0]);
		}
		
		$this->_id = $key_max;
		$this->session->location[$this->_id] = array('location_id'		=> $this->_id,
													'rsrc_id'			=> 1,
													'longitude'			=> $this->long,
										  			'latitude'			=> $this->lat,
										  		  	'description'		=> $this->description,
										  		  	'street_address'	=> $this->streetAddress,
										  		  	'city'				=> $this->city,
										  		  	'state'				=> $this->state,
										  		  	'zip'				=> $this->zip,
										  		  	'country'			=> $this->country,
										  		   	'primary_location' 	=> $this->primary);
	}
	
	/**
	 * Updates the long and lat info of a location
	 * when the user moves the marker manually
	 *
	 * @param int $location_id
	 * @param string $long
	 * @param string $lat
	 */
	public function updateLocation($location_id, $long, $lat )
	{
		$location = $this->session->location[$location_id];
		
		$this->session->location[$location['location_id']] = array('location_id'		=> $location['location_id'],
																	'rsrc_id'			=> $location['rsrc_id'],
																	'longitude'			=> $long,
										  							'latitude'			=> $lat,
										  		  					'description'		=> $location['description'],
										  		  					'street_address'	=> $location['street_address'],
										  		  					'city'				=> $location['city'],
										  		  					'state'				=> $location['state'],
										  		  					'zip'				=> $location['zip'],
										  		  					'country'			=> $location['country'],
										  		   					'primary_location' 	=> $location['primary_location']);
	}
	
	
	/**
	 * Removes a location from the mapSession
	 * session
	 *
	 * @param int array index $index
	 */
	public function deleteLocation($location_id)
	{
		unset($this->session->location[$location_id]);
	}
	
	/**
	 * Moves the primary_location to the next
	 * location in the array in case the user deletes
	 * her primary_location from the map.
	 * 
	 * NOTE: This method assumes there is NO location
	 * set to primary_location = 1.
	 * 
	 * If there is it gets wiped out. You should be checking
	 * that in the calling routine.
	 *
	 */
	public function updatePrimaryLocation()
	{
		//$this->session->location = array_values($this->session->location);
		$result = $this->session->location;
		
		// if there are no return results, we are done
		if (!$result) {
			return;
		}
		
		$loop = 1;
		
		foreach ($result as $key => $value) {
			if ($loop == 1 && $result[$key]['primary_location'] != self::PRIMARY_LOCATION ) {
        		// Update the row to primary_location = 1;
        		$result[$key]['primary_location'] = self::PRIMARY_LOCATION;
			} else {
        		// Update the row to primary_location = 0;
        		if ($result[$key]['primary_location'] == self::PRIMARY_LOCATION) {
            		$result[$key]['primary_location'] = self::OTHER_LOCATION ;
        		}
			}
			
			$loop++;
		}
		
		// assign the new values back to the session
        $this->session->location = $result;
	}
	
	/**
	 * Removes the mapSession session
	 *
	 */
	public static function deleteMapSession()
	{
		Zend_Session::namespaceUnset('mapSession');
	}
}