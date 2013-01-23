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

$newFile = 'utility_resourceViewUpdater.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();

// Vars
$totalResources = $y3Obj->getResourceCount();
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the view count {$totalResources} resources from Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getResourceIds($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$rsrcViewNum = $y3Obj->getResourceViewCount($value['rsrc_id']);
			
			// Insert statement!!
			$sql = "UPDATE resource SET views_lifetime = {$rsrcViewNum} WHERE rsrc_id = {$value['rsrc_id']};\n";
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