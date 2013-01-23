<?php
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('phpBB.class.php');

function cleanText($text) {
	
	// strip the html and slashes
	$text = trim(htmlspecialchars(stripslashes($text)));
	
	// Clean the comments with this regex
	$filters = array(
	    // replace non-alphanumeric characters with nothing
	    '/[^a-z0-9.,?&\-():;!=\'\[\]\/\n]+/i' => ' ',
	
	    // replace multiple spaces with a single space
	    '/ +/'          => ' '
	);

	// apply each replacement
	foreach ($filters as $regex => $replacement)
	    $cleanText = preg_replace($regex, $replacement, $text);
	    
	return $cleanText;
}

$newFile = 'phpBBPrivMessageBodyImport.sql';

$file = fopen($newFile, 'w');

// Objects
$privMsgObj = new PhpBB();

// Vars
$totalMessages = $privMsgObj->getPrivateBodyCount();
$limit = 1000;

// Here we go!
$timer->start();
echo "Migrating {$totalMessages} private message bodies into Yehoodi 3!\n\n";
for ($offset = 0; $offset <= $totalMessages; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$phpbbPrivateBodys = $privMsgObj->getPrivateBodys($limit, $offset);


	foreach ($phpbbPrivateBodys as $value) {
		
		echo ".";
		$id = $value['privmsgs_text_id'];
		$bbcode_uid = $value['privmsgs_bbcode_uid'];
		$body = cleanText($value['privmsgs_text']);
		//$body = $body . " ";
		
		$sql .= "INSERT INTO mail_body SET mail_id = {$id}, mail_body = \"{$body}\", bbcode_uid = \"{$bbcode_uid} \";\n";
		}
		fwrite($file,$sql);
		$sql = "";
	}

fclose($file);
echo "\nDone!";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";