<?php
/*
* This utility script updates all resources to the correct
* view count for the filter to work
*/
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

ini_set("memory_limit", "1000M");
require('Yehoodi3.class.php');

$newFile = 'utility_userCommentCountUpdater.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();

// Vars
$totalUsers = $y3Obj->getUserCount();
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the comment count for {$totalUsers} users on Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalUsers; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getUserIds($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$rsrcCount = $y3Obj->getUserResourceCommentCount($value['user_id']);
			$commentCount = $y3Obj->getUserCommentCount($value['user_id']);
			
			$total = $rsrcCount + $commentCount;
			
			// Insert statement!!
			$sql = "UPDATE user SET post_count = {$total} WHERE user_id = {$value['user_id']};\n";
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