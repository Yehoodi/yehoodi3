<?php
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

$dir    = 'oldubb';
$files1 = scandir($dir);
$sqlFile = 'ubbUsers.sql';
$sql = null;
$file = fopen($sqlFile, 'w');

//var_dump($files1);die;

foreach ($files1 as $fileEntry){
	if ($fileEntry == "." || $fileEntry == "..") {
		// skip the dot files
	} else {
		$value = file($dir . DIRECTORY_SEPARATOR . $fileEntry,FILE_IGNORE_NEW_LINES);
		
		$signUpDate = rtrim($value[10]);
		
		$month = substr($signUpDate,0,2);
		$day = substr($signUpDate,3,2);
		$year = substr($signUpDate,6,4);
		//echo $year."\n";
		
		$year == 99 ? $newYear = '19'.$year : $newYear = $year;
		
		$signUpDate = $newYear . '-' . $month . '-' . $day;
		
		$sql .= "INSERT INTO temp_ubb_dates SET user_name = \"".rtrim($value[0])."\", join_date = '".$signUpDate."';\n";
	}
}

file_put_contents($sqlFile, $sql);
fclose($file);
