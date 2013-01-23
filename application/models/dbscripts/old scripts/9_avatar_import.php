<?php

/**
 * This handles importing the avatars from Yehoodi 2 to the new db structure
 * 
 * Copy all avatars from Yehoodi 2 into
 * the dbscripts/avatars dir.
 * 
 * Make sure to get the avatars in the gallery directory also and
 * copy those directories into the dbscripts/avatars dir also.
 * 
 * This script takes care of the copy to the new data/avatars directory and
 * creates the phpBBAvatarImport.sql file for later import into the
 * user_avatar table on Yehoodi 3
 */
ini_set('display_errors','Off');

require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('phpBB.class.php');
function stristr_reverse($haystack, $needle) {
  $pos = stripos($haystack, $needle) + strlen($needle);
  return substr($haystack, 0, $pos -1);
} 

$newFile = 'phpBBAvatarImport.sql';

$avatarSourceDir = "avatars".DIRECTORY_SEPARATOR;

$avatarDestDir = ".."
				.DIRECTORY_SEPARATOR.".."
				.DIRECTORY_SEPARATOR.".."
				.DIRECTORY_SEPARATOR."data"
				.DIRECTORY_SEPARATOR."avatars"
				.DIRECTORY_SEPARATOR;

// Objects
$avatarObj = new PhpBB();

// Vars
$totalAvatars = $avatarObj->getAvatarCount();
$limit = 1000;
$counter = 1;
// Here we go!
$timer->start();
echo "Importing {$totalAvatars} avatars from the old Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalAvatars; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset;

	$phpbbAvatars = $avatarObj->getAvatars($limit, $offset);
	
	foreach ($phpbbAvatars as $value) {
		$userId = $value['user_id'];
		$sourceName = $value['user_avatar'];
		$targetName = basename($value['user_avatar']);
		
		//echo "Copying " .$avatarSourceDir.$sourceName."\nto". $avatarDestDir.$counter."\n";
		echo ".";
		
		copy( $avatarSourceDir . $sourceName, $avatarDestDir . $counter );
		$sql .= "INSERT INTO user_avatar SET avatar_id = {$counter}, user_id = {$userId}, filename = '{$targetName}';\n";
		
		$counter++;
		
	}
}
file_put_contents($newFile, $sql);
echo "\nDone.";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";
