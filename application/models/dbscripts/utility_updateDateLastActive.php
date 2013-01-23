<?php

/**
 * This utility updates the 'date_last_active'
 * field in the resource table so that sorting
 * by activity isn't a royal pain in the ass
 * anymore.
 * 
 */

ini_set("memory_limit", "64M");
require('Yehoodi3.class.php');
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

$newFile = 'utility_updateDateLastActive.sql';

$file = fopen($newFile, 'w');

// Objects
$resourceObj = new Yehoodi3();

// Vars
$totalResources = $resourceObj->getResourceCount();
$limit = 1000;
$sql = '';

//var_dump($postObj->getResources());die;
// Here we go!
$timer->start();
echo "Updating date_last_active in {$totalResources} total resources!\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset." of {$totalResources}.\n";
	$resources = $resourceObj->getResourceIdAndDate($limit, $offset);


	foreach ($resources as $value) {
		
		echo ".";
		$rsrcId = $value['rsrc_id'];

		// Get the comments for this resource id
		$comments = $resourceObj->getLastCommentDate($rsrcId);
		
		// Insert statement!!
		if ($comments) {
		  $sql .= "UPDATE resource SET date_last_active = '{$comments}' WHERE rsrc_id = {$rsrcId};\n";
		} else {
		  $sql .= "UPDATE resource SET date_last_active = '{$value['rsrc_date']}' WHERE rsrc_id = {$rsrcId};\n";
		}
		
		// Write to file
		fwrite($file,$sql);
		$sql = "";
	}
}

//fclose($file);
echo "\nDone!";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";