<?php

//TODO: Make these use the DateTime class and find a better place to put them (UtilityController?!?!)

/**
 * Common Yehoodi Functions
 *
 * @param unknown_type $time_stamp
 * @param unknown_type $time_zone
 * @return unknown
 */
class common
{
	public function __construct(){
	
	}

	/**
	 *  This function converts the phpBB timestamp format (from the phpBB cookie) into a friendly, readable format 
	 * returns February 2, 2001 8:30 PM
	 * 
	 * @param date $time_stamp
	 * @param int $time_zone
	 * @return string date
	 */
	public static function neatDate($time_stamp,$time_zone = 0)
	{
		$converted_date = date("l, F j, Y", mktime((substr($time_stamp,11,2)+$time_zone),substr($time_stamp,14,2),0,substr($time_stamp,5,2),substr($time_stamp,8,2),substr($time_stamp,0,4)));
		return $converted_date;
	}
	
	/**
	 * Returns the date in human readable format
	 * returns 10/21/2009 8:30pm
	 * 
	 * @param date $time_stamp
	 * @param int $time_zone
	 * @return string date
	 */
	public static function shortDateTime($time_stamp,$time_zone = 0)
	{
		$converted_date = date("n/j/y g:i a", mktime((substr($time_stamp,11,2)+$time_zone),substr($time_stamp,14,2),0,substr($time_stamp,5,2),substr($time_stamp,8,2),substr($time_stamp,0,4)));
		return $converted_date;
	}
	
	/**
	 * Returns the date in human readable format
	 * returns 10/21/2009 8:30pm
	 * 
	 * @param date $time_stamp
	 * @param int $time_zone
	 * @return string date
	 */
	public static function shortDate($time_stamp,$time_zone = 0)
	{
		$converted_date = date("n/j/y", mktime((substr($time_stamp,11,2)+$time_zone),substr($time_stamp,14,2),0,substr($time_stamp,5,2),substr($time_stamp,8,2),substr($time_stamp,0,4)));
		return $converted_date;
	}
	
	/**
	 * Returns the date in human readable format
	 * Doesn't include the year.
	 * 
	 * Sunday, January 4
	 *
	 * @param date $time_stamp
	 * @param int $time_zone
	 * @return string date
	 */
	public static function neatDateNoYear($time_stamp,$time_zone = 0)
	{
		$converted_date = date("l, F j", mktime((substr($time_stamp,11,2)+$time_zone),substr($time_stamp,14,2),0,substr($time_stamp,5,2),substr($time_stamp,8,2),substr($time_stamp,0,4)));
		return $converted_date;
	}
	
	/**
	 * Returns the birthday of the user for profile page
	 * Doesn't include the year.
	 * 
	 * January 4
	 *
	 * @param date $time_stamp
	 * @param int $time_zone
	 * @return string date
	 */
	public static function neatBirthDate($time_stamp,$time_zone = 0)
	{
		$converted_date = date("F j", mktime((substr($time_stamp,11,2)+$time_zone),substr($time_stamp,14,2),0,substr($time_stamp,5,2),substr($time_stamp,8,2),substr($time_stamp,0,4)));
		return $converted_date;
	}
	
	/**
	 * neatDateTime
	 *
	 * @param date timestamp $time_stamp
	 * @param int $time_zone
	 * @return string
	 */
	public static function neatDateTime($time_stamp,$time_zone = 0)
	{
		$converted_date = date("l, F j, Y g:i a", mktime((substr($time_stamp,11,2)+$time_zone),substr($time_stamp,14,2),0,substr($time_stamp,5,2),substr($time_stamp,8,2),substr($time_stamp,0,4)));
		return $converted_date;
	}
	
	/**
	* Checks the format of the given date
	* Returns false if it is malformed
	*
	* @param mysql date $date
	* @return bool
	*/
	public static function checkDateFormat($date)
	{
		//match the format of the date
		if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $date, $parts))
		{
			//check weather the date is valid of not
			if(checkdate($parts[2],$parts[3],$parts[1])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public static function plural($num) {
		if ($num != 1)
			return "s";
	}
       
	/**
	 * Returns the relative time for a given date
	 * (3 weeks ago)
	 *
	 * @param mysql date $date
	 * @return string
	 */
	public static function getRelativeTime($date) {
		$diff = time() - strtotime($date);

		if ($diff < 0)
			return;

		if ($diff<60)
			return $diff . " second" . common::plural($diff) . " ago";
		$diff = round($diff/60);

		if ($diff<60)
			return $diff . " minute" . common::plural($diff) . " ago";
		$diff = round($diff/60);

		if ($diff<24)
			return $diff . " hour" . common::plural($diff) . " ago";
		$diff = round($diff/24);

		if ($diff<7)
			return $diff . " day" . common::plural($diff) . " ago";
		$diff = round($diff/7);

		if ($diff<4.75)
			return $diff . " week" . common::plural($diff) . " ago";
		$diff = round($diff/4.75);

		if ($diff<12)
			return $diff . " month" . common::plural($diff) . " ago";
		$diff = round($diff/12);

		if ($diff<10)
			return $diff . " year" . common::plural($diff) . " ago";
		$diff = round($diff/10);

		//return "on " . date("F j, Y", strtotime($date));
		return "Over 10 years ago";	
	}
    
	/**
	 * Gets the size of a path
	 * (Used on the Admin stats page)
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	function getDirectorySize($path)
    {
      $totalsize = 0;
      $totalcount = 0;
      $dircount = 0;
      if ($handle = opendir ($path))
      {
        while (false !== ($file = readdir($handle)))
        {
          $nextpath = $path . '/' . $file;
          if ($file != '.' && $file != '..' && !is_link ($nextpath))
          {
            if (is_dir ($nextpath))
            {
              $dircount++;
              $result = getDirectorySize($nextpath);
              $totalsize += $result['size'];
              $totalcount += $result['count'];
              $dircount += $result['dircount'];
            }
            elseif (is_file ($nextpath))
            {
              $totalsize += filesize ($nextpath);
              $totalcount++;
            }
          }
        }
      }
      closedir ($handle);
      $total['size'] = $totalsize;
      $total['count'] = $totalcount;
      $total['dircount'] = $dircount;
      return $total;
    }

    /**
     * Returns a more friendly readable size string.
     *
     * @param unknown_type $size
     * @return string
     */
    function sizeFormat($size)
    {
        if($size<1024)
        {
            return $size." bytes";
        }
        else if($size<(1024*1024))
        {
            $size=round($size/1024,1);
            return $size." KB";
        }
        else if($size<(1024*1024*1024))
        {
            $size=round($size/(1024*1024),1);
            return $size." MB";
        }
        else
        {
            $size=round($size/(1024*1024*1024),1);
            return $size." GB";
        }
    
    }
    
    /**
     * diff() and htmlDiff() are used for the comment and topic
     * revision pop-ups so mods can see what was changed in an
     * edited comment / topic.
     * 
     * This was pulled from
     * http://paulbutler.org/archives/a-simple-diff-algorithm-in-php/
     *
     * @param string $old
     * @param string $new
     * @return string
     */

    /*
    	Paul's Simple Diff Algorithm v 0.1
    	(C) Paul Butler 2007 <http://www.paulbutler.org/>
    	May be used and distributed under the zlib/libpng license.
    	
    	This code is intended for learning purposes; it was written with short
    	code taking priority over performance. It could be used in a practical
    	application, but there are a few ways it could be optimized.
    	
    	Given two arrays, the function diff will return an array of the changes.
    	I won't describe the format of the array, but it will be obvious
    	if you use print_r() on the result of a diff on some test data.
    	
    	htmlDiff is a wrapper for the diff command, it takes two strings and
    	returns the differences in HTML. The tags used are <ins> and <del>,
    	which can easily be styled with CSS.  
    */
    function diff($old, $new){
    	$maxlen = 0;
        foreach($old as $oindex => $ovalue){
    		$nkeys = array_keys($new, $ovalue);
    		foreach($nkeys as $nindex){
    			$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
    				$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
    			if($matrix[$oindex][$nindex] > $maxlen){
    				$maxlen = $matrix[$oindex][$nindex];
    				$omax = $oindex + 1 - $maxlen;
    				$nmax = $nindex + 1 - $maxlen;
    			}
    		}	
    	}
    	if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
    	return array_merge(
    		common::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
    		array_slice($new, $nmax, $maxlen),
    		common::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }

    function htmlDiff($old, $new){
    	$ret = '';
        $diff = common::diff(explode(' ', $old), explode(' ', $new));
    	foreach($diff as $k){
    		if(is_array($k))
    			$ret .= (!empty($k['d'])?"<strike>".implode(' ',$k['d'])."</strike> ":'').
    				(!empty($k['i'])?"<u>".implode(' ',$k['i'])."</u> ":'');
    		else $ret .= $k . ' ';
    	}
    	return $ret;
    }

}