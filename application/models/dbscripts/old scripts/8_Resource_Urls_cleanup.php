<?php
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('Yehoodi3.class.php');

//$newFile = 'resourceUrlCleanup.sql';

// Objects
$resourceObj = new Yehoodi3();

// Vars
$totalResourceUrls = $resourceObj->getResourceUrlCount();
//$totalResourceUrls = 0;
$limit = 1000;

// Here we go!
$timer->start();
echo "Replacing duplicate urls from {$totalResourceUrls} in the resource_url table.\n\n";
for ($offset = 0; $offset <= $totalResourceUrls; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset;
	$resourceData = $resourceObj->getResourceUrl($limit, $offset);

	foreach ($resourceData as $value) {
		
		echo ".";
		$url = $value['rsrc_url'];
		
		$titleCount = $resourceObj->getUrlCount($url);
		
		if( $titleCount > 1 ) {
			$urlDupes = $resourceObj->getUrlsByTitle($url);
			$counter = 1;
			foreach ($urlDupes as $value) {
				$newUrl = $value['rsrc_url'] . '-' . $counter++;
				$resourceObj->processUrlUpdate($newUrl, $value['url_id']);
			}
		}
	}

	unset($resourceData);
}

//print_r($output);
//echo $sql;

//file_put_contents($newFile, $sql);
echo "\nUpdating table complete!";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";