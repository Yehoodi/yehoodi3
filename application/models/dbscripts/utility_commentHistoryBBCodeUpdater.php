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

$newFile = 'utility_commentHistoryBBCodeUpdater.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();
$bbcode = new BBcode();

// Vars
$totalComments = $y3Obj->getCommentHistoryCount();
//$totalComments = 1000;
$limit = 1000;

// Here we go!
$timer->start();
echo "Updating the comment_history comment {$totalComments} from Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalComments; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getCommentHistoryText($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";
			$commentId = $value['comment_id'];
			$commentText = $value['comment'];
			
			
            if (stripos($commentText, '<p>') === 0) {
                $commentText = substr($commentText, 3);
                
                $revString = strrev($commentText);

                if (stripos($revString, '>p/<') === 0) {
                    $revString = substr($revString, 4);
                }
                
                $commentText = strrev($revString);
            }

            //var_dump($commentText);die;
            
            $commentText = str_replace('&lt;BR&gt;', ',<br />', $commentText);
        	$commentText = str_replace('&lt;P&gt;', ',<br /><br />', $commentText);
        	$commentText = htmlentities($commentText);
			
			// Insert statement!!
			$sql = "UPDATE comment_history SET comment = \"{$commentText} \" WHERE comment_id = {$commentId};\n";
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