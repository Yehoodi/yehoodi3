<?php
/**
 * This utility creates a file that will later update all 
 * signatures from BBCode back to regular
 * html codes. This is since we are removing BBCode from Yehoodi 3.1
 * and switching to MarkDown (WMD).
 * 
 * For now, this is reading all the sigs and doing the following:
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

$newFile = 'utility_sigBBCodeUpdater.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();
$bbcode = new BBcode();

// Vars
$totalSignatures = $y3Obj->getSigCount();
//$totalSignatures = 1000;
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the sig {$totalSignatures} resources from Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalSignatures; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getSignatureText($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$userId = $value['user_id'];
			$signatureText = $value['signature'];
			$signatureBBCodeUID = $value['bbcode_uid'];
			
            //BBCode parsing for display - Looping over the text and BBCoding it
        	$parseCount = substr_count($signatureText, 'quote:');
        	
        	if ($parseCount > 1) {
    	        for ($i = 1; $i < $parseCount; $i++ ) {
                	//$signatureText = nl2br($bbcode->parse($signatureText, 'post', $signatureBBCodeUID));
                	$signatureText = $bbcode->parse($signatureText, 'post', $signatureBBCodeUID);
            	}
        	} else {
            	//$signatureText = nl2br($bbcode->parse($signatureText, 'post', $signatureBBCodeUID));
            	$signatureText = $bbcode->parse($signatureText, 'post', $signatureBBCodeUID);
        	}
        	
        	$signatureText = str_replace('&lt;BR&gt;', ',<br />', $signatureText);
        	$signatureText = str_replace('&lt;P&gt;', ',<br /><br />', $signatureText);
        	$signatureText = htmlentities($signatureText);
			
			// Insert statement!!
			$sql = "UPDATE user_profile SET profile_value = \"{$signatureText} \" WHERE user_id = {$userId} AND profile_key = 'sig';\n";
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