<?php

/**
 * This utility is to re-synch
 * the comment_num column in the 
 * resource table. This should be run
 * last if the migrate.sh script is run.
 * 
 */

ini_set("memory_limit", "64M");
require('Yehoodi3.class.php');
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

$newFile = 'utility_resynchCommentNum.sql';

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
echo "Updating comment_num in {$totalResources} total resources!\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset." of {$totalResources}.\n";
	$resources = $resourceObj->getResourceIds($limit, $offset);


	foreach ($resources as $value) {
		
		echo ".";
		$rsrcId = $value['rsrc_id'];

		// Get the comments for this resource id
		$comments = $resourceObj->getCommentsForResource($rsrcId);
		
		$counter = 1;
		foreach ($comments as $val) {
			// Insert statement!!
			$sql .= "UPDATE comment SET comment_num = {$counter} WHERE comment_id = {$val['comment_id']} AND rsrc_id = {$val['rsrc_id']};\n";
			$counter++;
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