<?php
/**
 * Handles the event dates for resources that have
 * multiple dates
 *
 */
class DatabaseObject_EventDate extends DatabaseObject
{


    public function __construct($db)
    {
        parent::__construct($db, 'event_date', 'event_id');

        // These are required
        $this->add('rsrc_id');
        $this->add('event_date');

    }

    protected function preInsert()
    {
    	return true;
    }

    protected function postLoad()
    {

    }

    protected function postInsert()
    {
    	return true;
    }

    protected function postUpdate()
    {
		return true;
    }

    protected function preDelete()
    {
    	return true;
    }

    public static function getEventDates($db, $options)
    {
    	// initialize the options
    	// Note: the limit in here messes up the search RebuildIndex method
    	$defaults = array(
    		'offset'	=> 0,
    		'limit'		=> 0,
    		'order'		=> ''
    	);
    	
        foreach ($defaults as $k => $v) {
            $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
        }
        
        // run the base query
        $select = self::_getBaseQuery($db, $options);
        
        // set the fields to select
        $select->from(null, 
        				array(  'ed.event_id',
								'ed.rsrc_id',
								'ed.event_date'
							)
        			 );
        
        // set the offset, limit, and ordering of results
        if ($options['limit'] > 0)
        	$select->limit($options['limit'], $options['offset']);
        	
        $select->order($options['order']);
        
        // fetch post data from the db
        //Zend_Debug::dump($select->__toString());die;
        $data = $db->fetchAll($select);

        // turn data into array of DatabaseObject_EventDate objects
        $evenDates = self::BuildMultiple($db, __CLASS__, $data);
        $evenDate_ids = array_keys($evenDates);
        
        if(count($evenDate_ids) == 0)
        	return array();
        	
        return $evenDates;
    } //getEventDates()
    
    /**
     * Used for the Dashboard Calendar
     * this pulls resource IDs for a specific
     * given date 
     *
     * @param database $db
     * @param array $options
     * @return rsrc_id array
     */
    public static function getEventsCountByDate($db, $options)
    {
    	// run the base query
        $select = self::_getBaseQuery($db, $options);
        
        // set the fields to select
        $select->from(null, 'count(*)'
        			)
				->join(array('r' => 'resource'),
        				'ed.rsrc_id = r.rsrc_id',
        						array()
        			);
		
        if (!empty($options['filter'])) {
        		$select->where('r.cat_id = (?)', $options['filter']
        			);
		}
		            
        // If the user wants distance browsing we must add the following
        // to our select object:
        if (!empty($options['distance'])) {
	            //Zend_Debug::dump($options['distance']);die;
        		$select->join(array('l' => 'location'),
        				'ed.rsrc_id = l.rsrc_id AND l.primary_location = 1',
        						array()
        						)
        				->where($options['distance']);
        }

		$select->where('r.is_active = ?', 1);

        if (!empty($options['order'])) {
        	$select->order($options['order']);
        }
        
        // fetch post data from the db
        //Zend_Debug::dump($select->__toString());
        $events = $db->fetchOne($select);

        return $events;
    } //getEventsByDate()
    
    /**
     * Used for the Dashboard Calendar
     * this pulls resource IDs for a specific
     * given date 
     *
     * @param database $db
     * @param array $options
     * @return rsrc_id array
     */
    public static function getEventsByDate($db, $options)
    {
    	// run the base query
        $select = self::_getBaseQuery($db, $options);
        
        // set the fields to select
        $select->from(null, 'r.rsrc_id'
        			)
				->join(array('r' => 'resource'),
        				'ed.rsrc_id = r.rsrc_id',
        						array()
        			);
		
        if ($options['filter'] > 0) {
        		$select->where('r.cat_id = (?)', $options['filter']
        			);
		}
		            
        // If the user wants distance browsing we must add the following
        // to our select object:
        if (!empty($options['distance'])) {
	            //Zend_Debug::dump($options['distance']);die;
        		$select->join(array('l' => 'location'),
        				'ed.rsrc_id = l.rsrc_id AND l.primary_location = 1',
        						array()
        						)
        				->where($options['distance']);
        }

		$select->where('r.is_active = ?', 1);
		
        // set the offset, limit, and ordering of results
        if (!empty($options['limit'])) {
        	$select->limit($options['limit'], $options['offset']);
        }
        	
        if (!empty($options['order'])) {
        	$select->order($options['order']);
        }
        
        // fetch post data from the db
        //Zend_Debug::dump($select->__toString());die;
        $ids = $db->fetchAll($select);

        return $ids;
    }
    
    /**
     * Get the count of event dates
     *
     * @param db object $db
     * @param array $options
     * @return int
     */
    public static function getEventDateCount($db, $options)
    {
        $select = self::_getBaseQuery($db, $options);
        $select->from(null, 'count(*)'
        			);

        //Zend_Debug::dump($select->__toString());die;
        return $db->fetchOne($select);
    } // getEventDateCount


    private static function _getBaseQuery($db, $options)
    {
        // initialize the options
        $defaults = array(
            'cat_id' => array(),
            'from'    => '',
            'to'      => ''
        );

        foreach ($defaults as $k => $v) {
            $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
        }

        // instantiate a Zend select object
        $select = $db->select();

        // define the table to pull from
        $select->from(array('ed' => 'event_date'), array());


        // filter results on specified event ids (if any)
        if (!empty($options['event_id']))
            $select->where('ed.event_id in (?)', $options['event_id']);

        // filter results on specified rsrc ids (if any)
        if (!empty($options['rsrc_id']))
            $select->where('ed.rsrc_id in (?)', $options['rsrc_id']);

        // filter results on specified event_date (if any)
        if (!empty($options['event_date']))
            $select->where('ed.event_date in (?)', $options['event_date']);

        //Zend_Debug::dump($select->__toString());die;
        return $select;
    }

     /**
	 * Saves an Event resource to the database.
	 * This should only be called after saveResource
	 * method and never alone.
	 *  
	 * Pass the proper variables and get back either
	 * true or false
	 * 
	 *
	 * @param int $rsrc_id
	 * @param date $startDate (20081231 format)
	 * @param string $repeat "none", "monthly", "weekly"
	 * @param int $duration (One day through One Week)
	 * @param date $endDate (20081231 format)
	 * @return bool
	 */
	public static function saveEventDates($db, $id, $repetition = "none", $start_date,  $duration = 1, $end_date = '' ) {
		
		$startDate = new DateTime($start_date);
		
		if ($end_date)
			$endDate = new DateTime($end_date);
	
		$dateArray = array();
		
		switch ($repetition) {
			
			case 'none':
				// Date from start to end of duration
				for($i=0; $i < $duration; $i++) {
					$dateArray[] = $startDate->format("Y-m-d");
					$startDate->modify("+1 days");
				}
	
				break;
				
			case 'weekly':
				// Dates for every week
				while ( $startDate->format("Y-m-d") <= $end_date ):
	    			$dateArray[] = $startDate->format("Y-m-d");
	    			$startDate->modify("+1 week");
				endwhile;
				break;
				
			case 'monthly':
				// Dates for nth {weekday} of the month
	
				// Get individual date fields
				$startYear = $startDate->format('Y'); // 2007
				$startMonth = $startDate->format('m'); // 3
				$startDay = $startDate->format('d'); // 11
				$endYear = $endDate->format('Y'); // 2007
				$endMonth = $endDate->format('m'); // 3
				$endDay = $endDate->format('d'); // 11
				
				$dayOfWeek = $startDate->format('l'); // Sunday
				
				// Get the number of days in the start month
				$daysInMonth = $startDate->format('t');
				
				// Count the number of the user selected weekdays in start month
				$weekDayCount = 0;
				$weekInMonth = 1;
				
				$tempDate = new DateTime($startDate->format('Y').$startDate->format('n').$startDate->format('j'));
	
				// Loop through the current month: $i through $daysInMonth
				for($i=1; $i<=$daysInMonth; $i++) {
				    $tempDate->setDate( $startDate->format('Y'), $startDate->format('n'), $i);
				    if ( $tempDate->format('l') == $dayOfWeek ) {
				    	$weekDayCount++;
				    	// Is the current date the same as the user picked?
				    	if($i == $startDay) {
				    		// Grab the weekday Count or rather which week number we are in
				    		switch($weekDayCount) {
								case 1:
									$weekInMonth = 1;
									break;
								case 2:
									$weekInMonth = 2;
									break;
								case 3:
									$weekInMonth = 3;
									break;
								case 4:
									$weekInMonth = 4;
									break;
								case 5:
									$weekInMonth = 5;
									break;
				    		}
			    		}
			    	}
			    }
			    
	
				// loop through the months
				
				$topDate = $startYear.$startMonth.$startDay;
				$bottomDate = $endYear.$endMonth.$endDay;
				
				$lastDate = 0;
				
				// loop through the first of the month to the last day of the month
				for($i = $topDate; $i <= $bottomDate; $i = date("Ymd", mktime(0, 0, 0, substr(($i + $daysInCurrentMonth - $myDay + 1),4,2), substr(($i + $daysInCurrentMonth - $myDay + 1),6,2), substr(($i + $daysInCurrentMonth - $myDay + 1),0,4)))) {
							// Set the currently checked month's date fields for tracking
							$myMonth = substr($i,4,2);
							$myDay = substr($i,6,2);
							$myYear = substr($i,0,4);
							// define the end (last day) of the currently checked month
							
							$tempDate = new DateTime($myYear.$myMonth.$myDay);
							$daysInCurrentMonth = $tempDate->format('t');
							
							$myCount = 0;
		
							// Loop through the current Month and count the number of weeks
							for($j=1; $j<=$daysInCurrentMonth; $j++) {
								// If the curent day of the week = the user's chosen day of the week...
							    if( date("l", mktime(0, 0, 0, $myMonth, $j, $myYear)) == $dayOfWeek ) {
							    	$myCount++; // Increase week count counter
									// if weekInMonth (the week which the user's date occurred) = the currently counted week...
								    if($weekInMonth == $myCount) {
										$chosenDate = date("Ymd", mktime(0, 0, 0, $myMonth, $j, $myYear)); // Valid match
								    }
		
								    if($myCount == 4 && $weekInMonth == 5) {
										$chosenDate = date("Ymd", mktime(0, 0, 0, $myMonth, $j, $myYear));
								    }
							    
					    		}
				    		}
					    	// Only add valid dates to the array
				    		if($bottomDate  >= $chosenDate ) {
					    		$dateArray[] = $chosenDate;
					    	}
						}
				break;
				}
	
		        //Zend_Debug::dump($dateArray);die();
				foreach($dateArray as $value) {
			    	$data = array();
			        $data['rsrc_id'] = $id;
			        $data['event_date'] = $value;
		
			        if(!$db->insert(Zend_Registry::get('dbTableConfig')->tblEventDate, $data)) {
			        	$message = array();
			        	$message['event'] = "Event Save Failed";
			        	return FALSE;
			        }
		        }
				return true;
			}

		/**
		 * Deletes all EventDate ids from the 
		 * database for a specific resource id
		 *
		 * @param object $db
		 * @param array $options
		 * @return bool
		 */
		public static function deleteEventDates($db, $options)
		{
			if($db->delete('event_date', sprintf('%s = %d', 'rsrc_id', $options['rsrc_id']))) {
				return true;
			}
	
		return false;
		}

}