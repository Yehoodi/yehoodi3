<?php

ini_set("memory_limit", "64M");
require('phpBB.class.php');
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


$newFile = 'y2NewsImport.sql';
//$error_log = 'comment_errors.txt';

$file = fopen($newFile, 'w');
//$errors = fopen($error_log,'w');

// Objects
$y2NewsObj = new Yehoodi2();
$y3Obj = new Yehoodi3();

// Vars
$totalNews = $y2NewsObj->getYehoodi2NewsCount();
$limit = 1000;

//var_dump($postObj->getResources());die;
// Here we go!
echo "Migrating {$totalNews} stories from Yehoodi 2!\n\n";
for ($offset = 0; $offset <= $totalNews; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y2Data = $y2NewsObj->getYehoodi2News($limit, $offset);


	foreach ($y2Data as $value) {
		
		echo ".";

		// This checks if it has a TopicID and if it does
		// we can skip the row because it has already been 
		// entered in the previous migration script
		
		if ($value['TopicID'] == 0) {
			$userId = $y3Obj->getIdForUserName($value['Author']);
			
		// If we don't find a userID for some reason set it to Yehoodi News
		if ($userId == "")
			$userId = 4823;
			
			switch ($value['Region']) {
				
				// Set cat_id variable according to the RegionID
				
				// northeast
				case 8: //Connecticut 
				case 9: //Delaware 
				case 23: //Maine 
				case 25: //Maryland 
				case 26: //Massachusetts 
				case 34: //New Hampshire
				case 35: //New Jersey
				//case 37: //New York
				case 45: //Pennsylvania 
				case 47: //Rhode Island
					$catId = 1;
					break;
					
				// new york city
				case 37:
					$catId = 1;
					break;
					
				// northwest & west coast
				case 2: // Alaska AK
				case 4: // Arizona AZ
				case 6: //California CA
				case 7: //Colorado CO
				case 15:// Hawaii HI
				case 16: //Idaho    ID
				case 31: //Montana MT
				case 33: //Nevada NV
				case 43: //Oregon OR
				case 56: //Washington WA					
					$catId = 1;
					break;
					
				// southeast
				case 1: // Alabama AL
				case 10: //DC DC
				case 12: //Florida FL
				case 13: //Georgia GA
				case 21: //Kentucky KY
				case 29: //Mississippi MS
				case 38: //North Carolina NC
				case 48: //South Carolina SC
				case 49: //South Dakota SD
				case 50: //Tennessee TN
				case 55: //Virginia VA
				case 57: //West Virginia WV
					$catId = 1;
					break;
					
				// midwest
				case 17: // Illinois IL
				case 18: //Indiana IN
				case 19: //Iowa IA
				case 20: //Kansas KS
				case 27: //Michigan MI
				case 28: //Minnesota MN
				case 30: //Missouri MO
				case 32: //Nebraska NE
				case 39: //North Dakota ND
				case 41: //Ohio OH
				case 52: //Utah UT
				case 53: //Vermont VT
				case 58: //Wisconsin WI
				case 59: //Wyoming WY
					$catId = 1;
					break;
					
				// southwest
				case 5: // Arkansas AR
				case 22: // Louisiana LA
				case 36: // New Mexico NM
				case 42: // Oklahoma OK
				case 51: // Texas TX
					$catId = 1;
					break;
					
				// national
				case 0:
					$catId = 1;
					break;
					
				default:
					$catId = 34;
					break;
					
			}
			
			// TODO: Get this value from the last person to comment on this resource
			$lastCommentId = 0;
			
			$title = cleanText($value['Header']);
			$description = cleanText($value['Article']);
			
			if (!empty($value['FullArticle']))
				$description .= cleanText($value['FullArticle']);
				
			$url = "";
			$embedCode = "";
			$startDate = "0000-00-00 00:00:00";
			$endDate = "0000-00-00 00:00:00";
			$rsrcDate = $value['ActiveDate'] . " " . $value['ActiveTime'];
			
			// Insert statement!!
			$sql .= "INSERT INTO resource SET user_id = {$userId}, cat_id = {$catId}, last_comment_id = {$lastCommentId}, title = \"{$title}\", descrip = \"{$description}\", rsrc_date = \"{$rsrcDate}\", is_active = 1;\n";
			fwrite($file,$sql);
			$sql = "";
		}
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