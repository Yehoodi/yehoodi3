<?php

ini_set("memory_limit", "64M");
require('Yehoodi3.class.php');

$newFile = 'resourceUrlImport.sql';

// Objects
$resourceObj = new Yehoodi3();

// Vars
$totalResources = $resourceObj->getResourceCount();
$limit = 1000;

// Here we go!
echo "Inserting {$totalResources} from resource table to resource_url table.\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset;
	$resourceData = $resourceObj->getResourceTitle($limit, $offset);


	foreach ($resourceData as $value) {
		
		//echo ".";
		$rsrcId = $value['rsrc_id'];
		$rsrcUrl = $resourceObj->generateUniqueUrl($value['title'], $rsrcId);
	
		$sql .= "INSERT INTO resource_url SET rsrc_id = {$rsrcId}, rsrc_url = \"{$rsrcUrl}\";\n";
	}

	unset($resourceData);
}

//print_r($output);
//echo $sql;

file_put_contents($newFile, $sql);