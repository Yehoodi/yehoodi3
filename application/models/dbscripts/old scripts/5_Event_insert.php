<?php

ini_set("memory_limit", "64M");
require('Yehoodi3.class.php');

$newFile = 'EventInsert.sql';

$file = fopen($newFile, 'w');

// Objects
$y3Obj = new Yehoodi3();

// Vars
$totalEvents = count($y3Obj->getEventResources(999999,0));
$limit = 1000;

// Here we go!
echo "Moving {$totalEvents} events into Yehoodi 3 event table!\n\n";
for ($offset = 0; $offset <= $totalEvents; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:\n".$offset;
	$y3Data = $y3Obj->getEventResources($limit, $offset);


	foreach ($y3Data as $value) {
		
		echo ".";

		$rsrcId = $value['rsrc_id'];
		$eventDate = $value['start_date'];
		
		// Insert statement!!
		$sql .= "INSERT INTO event_date SET rsrc_id = {$rsrcId}, event_date = \"{$eventDate}\";\n";
		fwrite($file,$sql);
		$sql = "";
	}
	//die;
	//var_dump($value);
}

fclose($file);
echo "\nDone!";