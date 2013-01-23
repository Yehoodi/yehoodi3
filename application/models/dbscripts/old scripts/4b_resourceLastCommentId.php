<?php

ini_set("memory_limit", "64M");
require('Yehoodi3.class.php');

$newFile = 'resourceLastCommentIdUpdate.sql';

// Objects
$resourceObj = new Yehoodi3();

// Vars
$totalResources = $resourceObj->getResourceCount();
$limit = 1000;

// Here we go!
echo "Updating {$totalResources} resources with last_commentIds.\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset;
	$resourceData = $resourceObj->getResourceIds($limit, $offset);


	foreach ($resourceData as $value) {
		
		//echo ".";
		$rsrcId = $value['rsrc_id'];
	
		$lastCommentId = $resourceObj->getLastCommentId($rsrcId);
		
		if($lastCommentId){
			$sql .= "UPDATE resource SET last_comment_id = {$lastCommentId} WHERE rsrc_id = {$rsrcId};\n";
		}
	}
}

//print_r($output);
//echo $sql;

file_put_contents($newFile, $sql);