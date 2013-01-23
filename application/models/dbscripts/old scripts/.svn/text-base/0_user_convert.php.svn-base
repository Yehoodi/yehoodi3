<?php
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('Yehoodi2.class.php');
require('phpBB.class.php');
require('Y2Users.class.php');

$usersObj = new Y2Users();

// Get the tru active user list
$activeUsers = $usersObj->getActiveUsers();

// Get the total num from active users
$totalUsers = count($activeUsers);

$newFile1 = 'usersImport.sql';
$newFile2 = 'usersProfileImport.sql';

$file = fopen($newFile2, 'w');

echo "Migrating {$totalUsers} users into Yehoodi 3 db!\n\n";
$timer->start();

	$userData = $usersObj->getUserInfo($activeUsers);
	
	foreach ($userData as $value) {
		
		//echo ".";
		
		$dateFirstVisit = date("Y-m-d H:i:s", $value['user_regdate'] );
		$dateUserLastVisit = date("Y-m-d H:i:s", $value['user_lastvisit'] );
		//$interests = trim(htmlspecialchars($value['user_interests']));
		//$signature = trim(htmlspecialchars($value['user_sig']));
		$birthdate = "";
		if(is_null($value['user_viewemail']))
			$utilizeEmail = 0;
		$userType = 2;
		$date_last_updated = "0000-00-00 00:00:00";
	
		$sql .= "INSERT INTO user SET user_id = {$value['user_id']}, user_type = {$userType}, user_name = \"{$value['user_name']}\", password = \"{$value['user_password']}\", email_address = \"{$value['user_email']}\", date_first_visit = \"{$dateFirstVisit}\", date_last_active = \"{$dateUserLastVisit}\", date_last_updated = \"{$date_last_updated}\";\n";
	
		// Loop through the current row and create inserts for the user_profile table
		foreach ($value as $k => $v) {
			// we don't need these fields nor do we need empty fields
			if ($k == 'user_email' || $k == 'user_regdate' || $k == 'user_id' || $k == 'user_name' || $k == 'user_password' || $k == 'user_realname' || $k == 'user_sig' || empty($v)) {
				// skip, silly but whatever. I hate !=
			} else {
				// Birthday mod conversion
				if($k == 'birthdate') {
					if($v == '999999') {
						$v = '0000-00-00 00:00:00';
					} else {
						$v = realdate("Y-m-d 00:00:00",$v);
					}
				}
				
				if ($k == 'user_lastvisit') {
					$v = $dateUserLastVisit;
				}
				
				$sql2 .= "INSERT INTO user_profile SET user_id = {$value['user_id']}, profile_key = '{$k}', profile_value = \"".trim(htmlentities($v))."\";\n";
				//echo "USER_ID = {$value['user_id']} KEY = {$k} VALUE = {$v}\n";
			}
			
			fwrite($file, $sql2);
			$sql2 = "";
	
		}
}

file_put_contents($newFile1, $sql);
fclose($file);
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";

//// FUNCTIONS
//
// Create date/time from format and timezone
//
function create_date($format, $gmepoch, $tz)
{
	global $board_config, $lang;
	static $translate;

	if ( empty($translate) && $board_config['default_lang'] != 'english' )
	{
		@reset($lang['datetime']);
		while ( list($match, $replace) = @each($lang['datetime']) )
		{
			$translate[$match] = $replace;
		}
	}

	return ( !empty($translate) ) ? strtr(@gmdate($format, $gmepoch + (3600 * $tz)), $translate) : @gmdate($format, $gmepoch + (3600 * $tz));
}

// Add function realdate for Birthday MOD
// the originate php "date()", does not work proberly on all OS, especially when going back in time
// before year 1970 (year 0), this function "realdate()", has a mutch larger valid date range,
// from 1901 - 2099. it returns a "like" UNIX date format (only date, related letters may be used, due to the fact that
// the given date value should already be divided by 86400 - leaving no time information left)
// a input like a UNIX timestamp divided by 86400 is expected, so
// calculation from the originate php date and mktime is easy.
// e.g. realdate ("m d Y", 3) returns the string "1 3 1970"

// UNIX users should replace this function with the below code, since this should be faster
//
//function realdate($date_syntax="Ymd",$date=0) 
//{ return create_date($date_syntax,$date*86400+1,0); }

function realdate($date_syntax="Ymd",$date=0)
{
	global $lang;
	$i=2;
	if ($date>=0)
	{
	 	return create_date($date_syntax,$date*86400+1,0);
	} else
	{
		$year= -(date%1461);
		$days = $date + $year*1461;
		while ($days<0)
		{
			$year--;
			$days+=365;
			if ($i++==3)
			{
				$i=0;
				$days++;
			}
		}
	}
	$leap_year = ($i==0) ? TRUE : FALSE;
	$months_array = ($i==0) ?
		array (0,31,60,91,121,152,182,213,244,274,305,335,366) :
		array (0,31,59,90,120,151,181,212,243,273,304,334,365);
	for ($month=1;$month<12;$month++)
	{
		if ($days<$months_array[$month]) break;
	}

	$day=$days-$months_array[$month-1]+1;
	//you may gain speed performance by remove som of the below entry's if they are not needed/used
	return strtr ($date_syntax, array(
		'a' => '',
		'A' => '',
		'\\d' => 'd',
		'd' => ($day>9) ? $day : '0'.$day,
		'\\D' => 'D',
		'D' => $lang['day_short'][($date-3)%7],
		'\\F' => 'F',
		'F' => $lang['month_long'][$month-1],
		'g' => '',
		'G' => '',
		'H' => '',
		'h' => '',
		'i' => '',
		'I' => '',
		'\\j' => 'j',
		'j' => $day,
		'\\l' => 'l',
		'l' => $lang['day_long'][($date-3)%7],
		'\\L' => 'L',
		'L' => $leap_year,
		'\\m' => 'm',
		'm' => ($month>9) ? $month : '0'.$month,
		'\\M' => 'M',
		'M' => $lang['month_short'][$month-1],
		'\\n' => 'n',
		'n' => $month,
		'O' => '',
		's' => '',
		'S' => '',
		'\\t' => 't',
		't' => $months_array[$month]-$months_array[$month-1],
		'w' => '',
		'\\y' => 'y',
		'y' => ($year>29) ? $year-30 : $year+70,
		'\\Y' => 'Y',
		'Y' => $year+1970,
		'\\z' => 'z',
		'z' => $days,
		'\\W' => '',
		'W' => '') );
}
// End add - Birthday MOD
