<?php
/**
 * This utility creates a file that will later update all 
 * mail messages from BBCode back to regular
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

$newFile = 'utility_mailBBCodeUpdater.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();
$bbcode = new BBcode();

// Vars
$totalMessages = $y3Obj->getMessageCount();
//$totalMessages = 1000;
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the mail_body {$totalMessages} resources from Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalMessages; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getMessageText($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$mailId = $value['mail_id'];
			$mailText = $value['mail_body'];
			$mailBBCodeUID = $value['bbcode_uid'];
			
            //BBCode parsing for display - Looping over the text and BBCoding it
        	$parseCount = substr_count($mailText, 'quote:');
        	
        	if ($parseCount > 1) {
    	        for ($i = 1; $i < $parseCount; $i++ ) {
                	//$mailText = nl2br($bbcode->parse($mailText, 'post', $mailBBCodeUID));
                	$mailText = $bbcode->parse($mailText, 'post', $mailBBCodeUID);
            	}
        	} else {
            	//$mailText = nl2br($bbcode->parse($mailText, 'post', $mailBBCodeUID));
            	$mailText = $bbcode->parse($mailText, 'post', $mailBBCodeUID);
        	}
        	
        	$mailText = str_replace('&lt;BR&gt;', ',<br />', $mailText);
        	$mailText = str_replace('&lt;P&gt;', ',<br /><br />', $mailText);
        	$mailText = htmlentities($mailText);
			
			// Insert statement!!
			$sql = "UPDATE mail_body SET mail_body = \"{$mailText} \" WHERE mail_id = {$mailId};\n";
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