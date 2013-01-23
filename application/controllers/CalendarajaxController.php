<?php

/**
 * Yehoodi 3.0 CalendarController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 *
 */
class CalendarajaxController extends CustomControllerAction
{
	
	protected $identity;
    
    public function init()
	{
        parent::init();
        // get the user information
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {        
            $this->identity = $auth->getIdentity();
        }
	} 

	/**
     * Saves the calendar type to the users
     * profile.
     * 
     * calendarType = 'month' | 'week' | 'day'
     *
     */
	public function savecalendartypeAction()
    {
    	// save the calendar url so the user comes back to this page
    	$type = $this->_request->getParam('type');
    	$month = (int)$this->_request->getParam('month');
    	$day = (int)$this->_request->getParam('day');
    	$year = (int)$this->_request->getParam('year');
		// Turns off automatic rendering to the template
		$this->_helper->viewRenderer->setNoRender();

			
        	// Valid lists of types
        	$validTypes        = array('month',
        	                           'week');
        	                           
        	$validYears        = range(2010,2020);
        	                           
            if (in_array($type, $validTypes)) {
                $calendarType = "{$type}/";
            }
			
    	if (is_object($this->identity)) {
    	    // Save url in the user's profile
			$user = new DatabaseObject_User($this->db);
			$user->load($this->identity->user_id);

			$user->profile->calendar_type = $calendarType;
			if (!$month) {
			     $user->profile->calendar_month = "00";  // Special case since the calendar is zero indexed I
			} else {                                     // need to explicitly set a "0" for the value.
			     $user->profile->calendar_month = $month;
			}
			
			if (!in_array($year, $validYears)) {
                $user->profile->calendar_year = date("Y");			    
			} else {
                $user->profile->calendar_year = $year;
			}
			
			$user->profile->calendar_day = $day;
			$user->save();
		} else {
		    // Save the url in the user's session
            $calSession =  new Zend_Session_Namespace('calendarLinkSession');
		    //Zend_Debug::dump($session);die;
		     
			$calSession->type = $calendarType;
			if (!$month) {
			     $calSession->month = "00";  // Special case since the calendar is zero indexed I
			} else {                                     // need to explicitly set a "0" for the value.
			     $calSession->month = $month;
			}

			if (in_array($year, $validYears)) {
               $calSession->year = $year;
			}
			
			$calSession->day = $day;
		}
    }
    
    /**
     * Sets the location cookie after the user
     * presses the home location reset link
     *
     */
    public function gethomelocationAction()
    {
		// Turns off automatic rendering to the template
		$this->_helper->viewRenderer->setNoRender();
		
		// Home location info
		$location = array('lon' => (float) $this->identity->longitude,
						  'lat' => (float) $this->identity->latitude,
						  'loc' => utf8_encode($this->identity->location),
						  );
		
        $cookie = json_encode($location);

		// Set a location cookie
		$cookieName = Zend_Registry::get('serverConfig')->locationCookie;
    	$cookie_time = (3600 * 24 * 30); // 30 days
		setcookie($cookieName, $cookie, time() + $cookie_time, '/calendar', 'yehoodi.com');
		
		echo $this->identity->location;
    }
}