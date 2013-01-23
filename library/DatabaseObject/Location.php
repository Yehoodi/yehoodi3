<?php
    class DatabaseObject_Location extends DatabaseObject
    {
		public function __construct($db)
		{
		    parent::__construct($db, 'location', 'location_id');
		
		    $this->add('rsrc_id');
		    $this->add('longitude');
		    $this->add('latitude');
		    $this->add('description');
		    $this->add('street_address','');
		    $this->add('city','');
		    $this->add('state','');
		    $this->add('zip','');
		    $this->add('country','');
		    $this->add('primary_location');
		}
		
        protected function postInsert()
        {
        	return true;
        }

        protected function postUpdate()
        {

        }
        	
        protected function preDelete()
        {
        	return true;
        }
        
        /**
         * Returns the location's descriptive text
         *
         * @return string
         */
        public function getDescription()
		{
			return $this->description;
		}
		
		/**
		 * Loads locations for a given resource id
		 * or returns false
		 *
		 * @param int $rsrc_id
		 * @param int $location_id
		 * @return bool
		 */
		public function loadForResource($rsrc_id, $location_id)
		{
		    $rsrc_id     = (int) $rsrc_id;
		    $location_id = (int) $location_id;
		
		    if ($rsrc_id <= 0 || $location_id <= 0)
		        return false;
		
		    $query = sprintf(
		        'select %s from %s where rsrc_id = %d and location_id = %d',
		        join(', ', $this->getSelectFields()),
		        $this->_table,
		        $rsrc_id,
		        $location_id
		    );
		
		    return $this->_load($query);
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
		 * static method to fetch all locations for
		 * a given resource
		 *
		 * @param object $db
		 * @param array $options
		 * @return array of location objects
		 */
		public static function getLocations($db, $options = array())
		{
		    // initialize the options
		    $defaults = array('rsrc_id' => array());
		
		    foreach ($defaults as $k => $v)
		        $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		
		    $select = $db->select();
		    $select->from(array('l' => 'location'), 'l.*');
		
		    // filter results on specified post ids (if any)
		    if (count($options['rsrc_id']) > 0)
		        $select->where('l.rsrc_id in (?)', $options['rsrc_id']);
		
		    // fetch post data from database
            //Zend_Debug::dump($select->__toString());die;
		    $locations = $db->fetchAll($select);
		
		    //Zend_Debug::dump($locations);die;
		    return $locations;
		}
		
		/**
		 * Returns the number of locations based on a
		 * resource id
		 *
		 * @param db object $db
		 * @param int $id
		 * @return int
		 */
		public static function getLocationCount($db, $id)
		{
            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('l' => 'location'), array());

	        $select->from(null, 'count(*)');

            $select->where('l.rsrc_id = ?', $id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
			
		}
		
		/**
		 * Updates the primary location to another marker
		 * if the current primary gets deleted
		 *
		 */
		public static function updatePrimaryLocation($db, $id)
		{
            // instantiate a Zend select object
            $select = $db->select();

            // get the location_ids associated with this resource
	        $select->from(array('l' => 'location'), array()) ;

            $select->from(null, array('l.location_id',
            						  'l.primary_location'));

            $select->where('l.rsrc_id = ?', $id);
            
            //Zend_Debug::dump($select->__toString());die;
            $result = $db->fetchAll($select);
            
            if(!$result) {
            	return false;
            }
            
            $rows = count($result);
            //Zend_Debug::dump($result);die;
            
			$update = new DatabaseObject_Location($db);

			for ($count = 0; $count < $rows; $count ++) {

				if($count == 0 && $result[0]['primary_location'] != 1) {
            		// Update the row to primary_location = 1;
            		$update->load($result[0]['location_id']);
            		$update->primary_location = 1;
            		$update->save();
            	} else {
            		// Update the row to primary_location = 0;
            		$update->load($result[$count]['location_id']);
            		if ($update->primary_location == 1) {
	            		$update->primary_location = 0;
						$update->save();
            		}
            	}
            }
		}

		/**
		 * Returns the number of locations based on a
		 * resource id
		 *
		 * @param db object $db
		 * @param int $id
		 * @return int
		 */
		public static function getDescriptionText($db, $id)
		{
            // instantiate a Zend select object
            $select = $db->select();

            // define the table to pull from
            $select->from(array('l' => 'location'), array());

	        $select->from(null, 'l.description');

            $select->where('l.rsrc_id = ?', $id);
            $select->where('l.primary_location = ?', 1);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
			
		}
		
		/**
		 * Deletes all location ids from the 
		 * database for a specific resource id
		 *
		 * @param object $db
		 * @param array $options
		 * @return bool
		 */
		public static function deleteLocations($db, $options)
		{
			if($db->delete('location', sprintf('%s = %d', 'rsrc_id', $options['rsrc_id']))) {
				return true;
			}

		return false;
		}
		
    }