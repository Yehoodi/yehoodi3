<?php
/*
Pseudocode for importing photos attached to resources from Yehoodi 2
I think this HAS to be a utility script run at the end of the migration because then we have the rsrc_ids of these resource

Search the description field in resources for href code and img code.
The description field must be htmlspecialchars_decode ed first.

Example:

<a href="http://www.yehoodi.com/mrjesse/">
<img src="http://www.yehoodi.com/images/news_photos/2137.jpg" width="100" alt="Manu and Micah Lewis" border="0">

This usually is found at the begining of the text within a <table > element but it could be anywhere.

Example:

<table align="left" valign="top"><tr><td><img src="http://www.yehoodi.com/images/frimframlarge.gif" width="190" alt="Frim Fram"<border="1"></td></tr></table>


Store the url
Store the image src. Only bother with stuff from the /news_photos dir. Everything else is too old or not on our server.
Store the rsrc_id

Strip the <table> tag from the story.

copy the img src file to a temp dir on the server.

Everything we need is ready to create the updated resources...

- Fill the resource_image table
Create a SQL script that inserts the filename and rsrc_id for each image

Add the files to data/thumbnails and data/uploaded-files via the script.

- Original file gets copied to the uploaded-files/ dir with the rsrc_id as the name and no extension

Done.
*/

ini_set("memory_limit", "768M");
require('Yehoodi3.class.php');
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
require_once('simplehtmldom'.DIRECTORY_SEPARATOR.'simple_html_dom.php');

function get_first_image($html) {
    $post_dom = str_get_dom($html);
    $first_img = $post_dom->find('img', 0);
    $first_link = $post_dom->find('a', 0);
    $first_table = $post_dom->find('table', 0);

    if($first_img !== null) {
        return array($first_img->src, $first_link->href, $first_table->xmltext);
    }
    return null;
}

$timer = new Benchmark_Timer();

$newFile = 'utility_resource_imageConvert.sql';

$file = fopen($newFile, 'w');

// Objects
$resourceObj = new Yehoodi3();

// Vars
$totalResources = $resourceObj->getResourceCount();
$limit = 1000;
$sql = '';

// Here we go!
$timer->start();
echo "Updating descrip field in {$totalResources} total resources!\n\n";
for ($offset = 0; $offset <= $totalResources; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset." of {$totalResources}.\n";
	$resources = $resourceObj->getResourceIds($limit, $offset);
	
	foreach ($resources as $value) {
		
		echo ".";
		$rsrcId = $value['rsrc_id'];
		$imagePath = '';
		$url = '';
		$result = '';
		$filePath = '';
		$fileName = '';

		// Get the description for this resource id
		$html = htmlspecialchars_decode($resourceObj->getDescriptionForResource($rsrcId));
		
		$result = get_first_image($html);
		
		
		if ($result[1] && !strstr($result[1],'javascript')) {
			$url = $result[1];
		}
		
		if ($parsedUrl = parse_url($result[0])) {
			if ($parsedUrl['host'] == 'www.yehoodi.com' || $parsedUrl['host'] == 'yehoodi.com') {
				if (stripos($parsedUrl['path'],'news_photos')) {
					$filePath = $parsedUrl['path'];
					$fileName = str_replace('/','',strrchr($parsedUrl['path'],'/'));
				}
			}
		}

		if ($result[2]) {

			$html = str_replace($result[2],'',$html);
			$html = preg_replace('#<table(.*?)</table>#si','',$html);
			
			$descrip = htmlspecialchars($html);
		}
		
		//echo $rsrcId . "\n";
		if ($filePath) {
			//echo $filePath . "\n";
			//echo $fileName . "\n";die;
			// Insert statement!!
			$sql .= "INSERT INTO resource_image SET filename = '{$fileName}', rsrc_id = {$rsrcId};\n";

			$imageSourceDir = DIRECTORY_SEPARATOR."var"
							 .DIRECTORY_SEPARATOR."www"
							 .DIRECTORY_SEPARATOR."sites"
							 .DIRECTORY_SEPARATOR."yehoodi3.com"
							 .DIRECTORY_SEPARATOR."dev"
							 .DIRECTORY_SEPARATOR."trunk"
							 .DIRECTORY_SEPARATOR."application"
							 .DIRECTORY_SEPARATOR."models"
							 .DIRECTORY_SEPARATOR."dbscripts"
							 .DIRECTORY_SEPARATOR."var"
							 .DIRECTORY_SEPARATOR."www"
							 .DIRECTORY_SEPARATOR."sites"
							 .DIRECTORY_SEPARATOR."yehoodi2.com"
							 .DIRECTORY_SEPARATOR."news_photos"
							 .DIRECTORY_SEPARATOR;
			
			$imageDestDir =  DIRECTORY_SEPARATOR."var"
							 .DIRECTORY_SEPARATOR."www"
							 .DIRECTORY_SEPARATOR."sites"
							 .DIRECTORY_SEPARATOR."yehoodi3.com"
							 .DIRECTORY_SEPARATOR."dev"
							 .DIRECTORY_SEPARATOR."trunk"
							 .DIRECTORY_SEPARATOR."application"
							 .DIRECTORY_SEPARATOR."models"
							 .DIRECTORY_SEPARATOR."dbscripts"
							 .DIRECTORY_SEPARATOR."image_temp"
							 .DIRECTORY_SEPARATOR;
			
			//echo "source:{$imageSourceDir}{$fileName}\ndestination:{$imageDestDir}{$fileName}\n";die; 
				if(!($cp = @copy( $imageSourceDir.$fileName, $imageDestDir.$fileName )))
					echo "\n\nCouldn't copy {$imageSourceDir}{$fileName}\nto {$imageDestDir}{$fileName}";
		}
		
		if ($url) {
			$sql .= "UPDATE resource SET descrip = \"{$descrip}\", url = \"{$url}\" WHERE rsrc_id = {$rsrcId};\n";
			//echo $url . "\n\n";
		}
		

	}

	// Write to file
	fwrite($file,$sql);
	$sql = "";


}

// End of script
echo "\nDone!";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";