<?php
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('ubb.class.php');
require('Yehoodi3.class.php');

$newFile = 'ubbDateUpdate.sql';
$file = fopen($newFile, 'w');

// Objects
$ubbObj = new Ubb();
$y3Obj = new Yehoodi3();

$ubbTable = $ubbObj->getUbbUsers();
$users = $y3Obj->getUsers();

$timer->start();
echo "Converting";
foreach ($users as $value) {
	echo ".";
	foreach ($ubbTable as $v) {
		if ($value['user_name'] == $v['user_name']) {
			$sql .= "UPDATE user SET date_first_visit='{$v['join_date']}' WHERE user_name = \"{$value['user_name']}\";\n";
			break;
		}
	}
}

file_put_contents($newFile, $sql);
fclose($file);
$timer->stop();
echo "\nDone.";
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";