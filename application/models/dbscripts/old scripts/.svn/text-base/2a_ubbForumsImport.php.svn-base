<?php
/**
 * This bad boy converts all the old Ultimate Bulletin Board
 * posts from Yehoodi 1.0 to Yehoodi 3.0
 * 
 * Hooray! Back where they belong!
 * 
 */
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('Yehoodi3.class.php');
$userObj = new Yehoodi3();

/**
 * Converts "01:02 PM" to a 
 * MySQL date format
 *
 * @param ubb date string $time
 * @return MySQL date string
 */
function convertTime($time) {
	$hour = substr($time,0,2);
	$minute = substr($time,3,2);
	$meridian = substr($time,6,2);

	$meridian == 'PM' ? $hour = $hour + 12 : $hour ;

	if ($hour == 24)
		$hour = '00';
	
	return $hour.":".$minute;
}

function convertDate($date) {
	$month = substr($date,0,2);
	$day = substr($date,3,2);
	$year = substr($date,6,4);
	//echo $year."\n";
	
	$year == 99 ? $newYear = '19'.$year : $newYear = $year;
	
	$date = $newYear . '-' . $month . '-' . $day;
	
	return $date;
}

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

function checkIP($input) {
	
 if (preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $input)) {  
        return TRUE;  
     }  else  {  
		return FALSE;
     }
}

/**
 * @return uid generated unique identifier with a length of 10 characters
 */
function makeUID(){
    return substr(md5(mt_rand()), 0, 10);
}


// Directories
$subDirs = array('Forum1',
				 'Forum2',
				 'Forum3',
				 'Forum4',
				 'Forum5',
				 'Forum6',
				 'Forum7',
				 'Forum9'
			);

// Resource categories
$categories = array(0 => "26",	// The Kitchen Sink
					1 => "5",	// Swing Talk
					2 => "10",	// Workshop
					3 => "10",
					4 => "10",
					5 => "35",	// Music, Bands & Artists
					6 => "5",	// Swing Talk
					7 => "31"	// Support
			);

$dir    = 'oldubbforums';

//$files = scandir($dir);
//var_dump($files);die;

// files
$resourceSqlFile = 'ubbResourceImport.sql';
$commentSqlFile = 'ubbCommentImport.sql';

// Some defaults
$lastRsrcId = $userObj->getLastResourceId() + 1;
$lastCommentId = 0;
$closed = 0;
$sticky = 0;
$isActive = 1;
$catLoop = 0;

// SQL concatanated string
$rsrcSql = null;
$commentSql = null;

// Open files for writing
$file1 = fopen($resourceSqlFile, 'w');
$file2 = fopen($commentSqlFile, 'w');

echo "Starting uBB migration\n";

$timer->start();

// Directory Iteration
foreach ($subDirs as $sub ) {
	$files = scandir($dir . DIRECTORY_SEPARATOR . $sub);
	echo "\n\nWorking in directory: ".$dir . DIRECTORY_SEPARATOR . $sub."\n";

	// Iterate the files in the selected Forum directory
	foreach ($files as $fileEntry){
		echo "\nFile: {$fileEntry}";
		
		if ($fileEntry == "." || $fileEntry == "..") {
			// skip the dot files
		} else {
			// Open the forum's .cgi file into an Array for processing
			$cgiFileArray = file($dir . DIRECTORY_SEPARATOR . $sub . DIRECTORY_SEPARATOR . $fileEntry,FILE_IGNORE_NEW_LINES);
			
			foreach ($cgiFileArray as $key => $fileContents) {
				echo ".";
				
				// Get the resource from the first TWO lines in the .cgi file
				if ( $key == 0 ) {
					// explode the first line into an array
					$resourceArray = explode('|',$fileContents);
	
					// Gotta check for a real user
					if ($userId = $userObj->getIdForUserName($resourceArray[6])) {
	
						// Get the title and user id
						$title = cleanText($resourceArray[8]);
					}
	
				} elseif ( $key == 1 ) {
	
					if ($userId) {
						// explode the second line of the file
						$commentArray = explode('|',$fileContents);
						
						// Get and convert the ubb date/time
						$rsrcDate = $commentArray[6];
						$rsrcTime = $commentArray[8];
						
						// Store it here
						$resourceDate = convertDate($rsrcDate) . " " . convertTime($rsrcTime) . ":00";
						$descrip = cleanText($commentArray[12]);
						
						switch ( $sub ) {
							case 'Forum1':
								$catId = 26; //The Kitchen Sink
								break;
							
							case 'Forum2':
								$catId = 5; //Swing Talk
								break;
							
							case 'Forum3':
								$catId = 13;//Swing Clubs
								break;
							
							case 'Forum4':
								$catId = 5; //Swing Events
								break;
							
							case 'Forum5':
								$catId = 10;//Classes & Workshops
								break;
							
							case 'Forum6':
								$catId = 35; //Swing Bands/Music
								break;
							
							case 'Forum7':
								$catId = 5;//Sell And Buy
								break;
							
							case 'Forum9':
								$catId = 31;//Yehoodi Support
								break;
							
						}
						
						// write out the resource sql
						$rsrcSql .= "INSERT INTO resource SET rsrc_id = {$lastRsrcId}, user_id = {$userId}, cat_id = {$catId}, last_comment_id = {$lastCommentId}, title = \"{$title}\", descrip = \"{$descrip}\", rsrc_date = \"{$resourceDate}\", closed = {$closed}, sticky = {$sticky}, is_active = 1;\n";
						
						//echo "\n".$sub;
						//echo "\n".$catId;die;
						// reset comment counter
						$commentNum = 1;
					}
					
				} else {
					// Get the comment stuff
					if ($userId) {
						// explode the second line of the file
						$commentArray = explode('|',$fileContents);
						
						// Get and convert the ubb date/time
						$commentDate = $commentArray[6];
						$commentTime = $commentArray[8];
						$commentUserId = $commentArray[4];
						
						$commentUserId = $userObj->getIdForUserName($commentArray[4]);

						if (!$commentUserId) {
							// if we get no user id then set the comment to Yehoodi News for now
							$commentUserId = 4823;
						}
						
						// Store it here
						$dateCreated = convertDate($commentDate) . " " . convertTime($commentTime) . ":00";
						$comment = cleanText($commentArray[12]);
						$bbcode_uid = makeUID();
						$remoteIP = $commentArray[14];
						
						if (!checkIP($remoteIP))
							$remoteIP = '127.0.0.1';
						
						// Get the reply to user id
						$replyToId = $lastRsrcId;
						
						// write out the comment sql
						$commentSql .= "INSERT INTO comment SET rsrc_id = {$lastRsrcId}, user_id = {$commentUserId}, comment_num = {$commentNum}, comment = \"{$comment}\", reply_to_id = {$replyToId}, date_created = \"{$dateCreated}\", is_active = 1, bbcode_uid = \"{$bbcode_uid}\", remote_ip = INET_ATON('{$remoteIP}');\n";
						
						// increment comment counter
						$commentNum++;
	
					}
				}
			}
			// increment the $lastRsrcId
			$lastRsrcId++;
		}
		// increment the category id
		$catLoop++;
	}

}
	echo "\nWriting out files...";
	file_put_contents($commentSqlFile, $commentSql);
	file_put_contents($resourceSqlFile, $rsrcSql);
	$commentSql = '';
	$rsrcSql = '';

fclose($file2);

fclose($file1);

$timer->stop();

echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";