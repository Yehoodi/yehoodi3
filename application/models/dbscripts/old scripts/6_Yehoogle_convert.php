<?php

ini_set("memory_limit", "64M");
require('Yehoodi3.class.php');
require('Yehoodi2.class.php');

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

$newFile = 'YehoogleInsert.sql';

$file = fopen($newFile, 'w');

// Objects
$y2Obj = new Yehoodi2();
$y3Obj = new Yehoodi3();

// Vars
$totalYehoogles =$y2Obj->getYehoogleCount();
$limit = 1000;

// Here we go!
echo "Moving {$totalYehoogles} Yehoogles into Yehoodi 3!\n\n";
for ($offset = 0; $offset <= $totalYehoogles; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y2Data = $y2Obj->getYehoogles($limit, $offset);

	foreach ($y2Data as $value) {
		
		echo ".";

		$userId = $y3Obj->getIdForUserName($value['PostBy']);

		// If we don't find a userID for some reason set it to Yehoodi News
		if ($userId == "")
			$userId = 4823;

		$groupId = $y2Obj->getRsrcGroupID($value['rsrcID']);

		switch ($groupId) {
			
			case 7:
			case 22:
				$catId = 25; //shopping
				break;
				
			case 1:
			case 2:
			case 3:
				$catId = 35; //bands & artists
				break;
				
			case 5:
			case 11:
			case 23:
			case 12: // Misc Group ???
				$catId = 21; //dance performers & instructors
				break;
				
			case 4:
			case 13:
			case 15:
			case 16:
			case 17:
			case 18:
			case 19:
				$catId = 22; //online news / blogs
				break;
				
			case 8:
				$catId = 23; //studios & schools
				break;
				
			case 6:
			case 9:
			case 10:
				$catId = 24; //swing societies & clubs
				break;
				
			case 14:
			case 21:
				$catId = 5; //Media: Video
				break;
				
			case 20:
				$catId = 5; //Media: Images - Photos
				break;
		}
		
		$title = cleanText($value['rsrcName']);
		$description = cleanText($value['rsrcDescription']);
		$url = trim(htmlspecialchars(stripslashes($value['url'])));
		$location = cleanText($value['rsrcAddress1'] . " " . $value['rsrcCity'] . " " . $y2Obj->getStateById($value['rsrcState']));
		
		//$rsrcDate = date("Y-m-d H:m:s");
		$rsrcDate = "2004-01-04 12:00:00";
		
		// Insert statement!!
		//$sql .= "INSERT INTO resource SET user_id = {$userId}, cat_id = {$catId}, title = \"{$title}\", descrip = \"{$description}\", rsrc_date = \"{$rsrcDate}\", url = \"{$url}\", location = \"{$location}\", is_active = 1;\n";
		$sql .= "INSERT INTO resource SET user_id = {$userId}, cat_id = {$catId}, title = \"{$title}\", descrip = \"{$description}\", rsrc_date = \"{$rsrcDate}\", url = \"{$url}\", is_active = 1;\n";
		fwrite($file,$sql);
		$sql = "";
	}
	//die;
	//var_dump($value);
}

fclose($file);
echo "\nDone!";