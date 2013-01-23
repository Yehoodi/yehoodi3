<?php

ini_set("memory_limit", "64M");
require('Yehoodi2.class.php');
require('Yehoodi3.class.php');

function cleanText($text) {
	
	// strip the html and slashes
	$text = trim(htmlspecialchars(stripslashes($text)));
	
	// Clean the comments with this regex
	$filters = array(
	    // replace non-alphanumeric characters with nothing
	    '/[^a-z0-9.,?&\-():;!=\'\[\]\/\n]+/i' => ' ',
	
	    // replace multiple spaces with a single space
	    '/ +/'          => ' '
	);

	// apply each replacement
	foreach ($filters as $regex => $replacement)
	    $cleanText = preg_replace($regex, $replacement, $text);
	    
	return $cleanText;
}


$newFile = 'y2EventImport.sql';
//$error_log = 'comment_errors.txt';

$file = fopen($newFile, 'w');
//$errors = fopen($error_log,'w');

// Objects
$y2EventObj = new Yehoodi2();
$y3Obj = new Yehoodi3();

// Vars
$totalEvents = $y2EventObj->getEventCount();
$limit = 1000;

// Here we go!
echo "Migrating {$totalEvents} events from Yehoodi 2!\n\n";
for ($offset = 0; $offset <= $totalEvents; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y2Data = $y2EventObj->getEventData($limit, $offset);


	foreach ($y2Data as $value) {
		
		echo ".";

		$userId = $y3Obj->getIdForUserName($value['PostBy']);

		// If we don't find a userID for some reason set it to Yehoodi News
		if ($userId == "")
			$userId = 4823;
			
		// Determine Type of event (Comp, Workshop, etc...)
		switch ($value['TypeID']) {
		
			// Club
			case 1:
				$catId = 13;
				break;
				
			// Competition
			case 2:
				$catId = 9;
				break;
				
			// Exchange
			case 3:
				$catId = 11;
				break;
				
			// Show / Performance
			case 4:
				$catId = 14;
				break;
				
			// Workshop - Camp
			case 5:
				$catId = 10;
				break;
				
			// Special Event
			case 6:
				$catId = 14;
				break;
					
		}
			
			
		// Determine City
		if($value['City'])
			$location = $value['City'] . ", ";
		
		// Determine State
		if ($y2EventObj->getStateById($value['RegionID']))
			$location .= $y2EventObj->getStateById($value['RegionID']);
			
		$title = cleanText($value['Title']);
		$description = cleanText($value['Description']);
		
		$url = "";
		$embedCode = "";
		$startDate = $value['EventDate'];
		$lastCommentId = 0;
		
		
		// If the event is in the future, set the rsrc_date to right now
		$currentDate = date("Y-m-d H:i:s");
		if($value['EventDate'] > $currentDate) {
			$rsrcDate = $currentDate;
		} else {
			$rsrcDate = $value['EventDate'];
		}
		
		// Insert statement!!
		//$sql .= "INSERT INTO resource SET user_id = {$userId}, cat_id = {$catId}, last_comment_id = {$lastCommentId}, title = \"{$title}\", descrip = \"{$description}\", start_date = \"{$startDate}\", location = \"{$location}\", rsrc_date = \"{$rsrcDate}\", is_active = 1;\n";
		$sql .= "INSERT INTO resource SET user_id = {$userId}, cat_id = {$catId}, last_comment_id = {$lastCommentId}, title = \"{$title}\", descrip = \"{$description}\", start_date = \"{$startDate}\", end_date = \"{$startDate}\", rsrc_date = \"{$rsrcDate}\", is_active = 1;\n";
		fwrite($file,$sql);
		$sql = "";
		unset($location);
	}
	//die;
	//var_dump($value);
//unset($phpbbTopicData);
}

//print_r($output);
//echo $sql;

//file_put_contents($newFile, $sql);
fclose($file);
//fclose($errors);
echo "\nDone!";