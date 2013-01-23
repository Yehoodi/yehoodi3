<?php
/**
 * This utility creates a file that will later update all 
 * resource descriptions from BBCode back to regular
 * html codes. This is since we are removing BBCode from Yehoodi 3.1
 * and switching to MarkDown (WMD).
 * 
 * For now, this is reading all the descriptions and doing the following:
 * 
 * 1. Parsing the BBCode back into html
 * 2. Running the nl2br function
 * 3. Running htmlentities
 * 
 * Then the final stuff gets thrown into an UPDATE statement for later.
 * 
 */
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

ini_set("memory_limit", "1000M");
require('Yehoodi3.class.php');
require('BBcode.php');

$newFile = 'utility_resourceBBCodeUpdater.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();
$bbcode = new BBcode();

// Vars
$totalResources = $y3Obj->getResourceCount();
//$totalResources = 1000;
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the comment count {$totalResources} resources from Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getResourceDescriptions($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$rsrcId = $value['rsrc_id'];
			$rsrcDescrip = $value['descrip'];
			$rsrcBBCodeUID = $value['bbcode_uid'];
			
            //BBCode parsing for display - Looping over the text and BBCoding it
        	$parseCount = substr_count($rsrcDescrip, 'quote:');
        	
        	if ($parseCount > 1) {
    	        for ($i = 1; $i < $parseCount; $i++ ) {
                	//$rsrcDescrip = nl2br($bbcode->parse($rsrcDescrip, 'post', $rsrcBBCodeUID));
                	$rsrcDescrip = $bbcode->parse($rsrcDescrip, 'post', $rsrcBBCodeUID);
            	}
        	} else {
            	//$rsrcDescrip = nl2br($bbcode->parse($rsrcDescrip, 'post', $rsrcBBCodeUID));
            	$rsrcDescrip = $bbcode->parse($rsrcDescrip, 'post', $rsrcBBCodeUID);
        	}
        	
        	$rsrcDescrip = str_replace('&lt;BR&gt;', ',<br />', $rsrcDescrip);
        	$rsrcDescrip = str_replace('&lt;P&gt;', ',<br /><br />', $rsrcDescrip);
        	$rsrcDescrip = htmlentities($rsrcDescrip);
			
			// Insert statement!!
			$sql = "UPDATE resource SET descrip = \"{$rsrcDescrip}\" WHERE rsrc_id = {$rsrcId};\n";
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