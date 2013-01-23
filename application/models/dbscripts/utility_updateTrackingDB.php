<?php
/**
 * This utility creates a file that will later update
 * the comment_tracking table that keeps track of
 * the last post activity for all logged in users.
 * 
 * We need to add a 'comment_user_id' field to the
 * table and this script will update all the current
 * rows with the user_id of the particular rsrc_id and
 * comment_num.
 * 
 */
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

ini_set("memory_limit", "1000M");
require('Yehoodi3.class.php');
require('BBcode.php');

$newFile = 'utility_updateTrackingDB.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();

// Vars
$totalTrackingRows = $y3Obj->getTrackingCount();
//$totalTrackingRows = 1000;
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the {$totalTrackingRows} comment_user_id rows from Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalTrackingRows; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getTrackingInfo($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$trackingId = $value['track_id'];
			$rsrcId = $value['rsrc_id'];
			$commentNum = $value['comment_num'];
			
			// Get the user_id from the comment table
			$commentUserId = $y3Obj->getUserIdFromTrackInfo($rsrcId, $commentNum);
			
			// Insert statement!!
			$sql = "UPDATE comment_tracking SET comment_user_id = {$commentUserId} WHERE track_id = {$trackingId};\n";
			fwrite($file,$sql);
	}
}

fclose($file);
echo "\nDone!";

$timer->stop();
$seconds = round($timer->timeElapsed(),2);
if ($seconds < 60) {
	echo "Elapsed time was: " . round($timer->timeElapsed(),2) ." seconds.\n";
} else {
	echo "Elapsed time was: " . round($seconds / 60, 2) ." minutes.\n";
}