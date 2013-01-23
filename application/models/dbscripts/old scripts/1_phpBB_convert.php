<?php
ini_set("memory_limit", "64M");
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('phpBB.class.php');
require('Yehoodi2.class.php');

// Topics in phpBB Discussion Board become Resources
/**
 * topic_status 1 = locked thread
 * topic_type 1 = Sticky
 * 			  2 = Announcement
 * 
 */

$newFile = 'phpBBImport.sql';

// Objects
$topicObj = new PhpBB();
$postObj = new PhpBB();
$newsObj = new Yehoodi2();

// Vars
$totalPosts = $topicObj->getTopicCount();
$limit = 1000;

// Here we go!
$timer->start();
echo "Migrating {$totalPosts} posts from the old Yehoodi!\n\n";
for ($offset = 0; $offset <= $totalPosts; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset;
	$phpbbTopicData = $topicObj->getTopics($limit, $offset);


	foreach ($phpbbTopicData as $value) {
		
		echo ".";
		$topicId = $value['topic_id'];
		$userId = $value['topic_poster'];
	
		switch ($value['forum_id']) {
			// Swing Talk
			case 1:
				$catId = 5;
				break;
	
			// Swing Music / Bands
			case 2:
				$catId = 35;
				break;
	
			// Classes & Workshops
			case 3:
				$catId = 10;
				break;
	
			// The Kitchen Sink
			case 5:
				$catId = 26;
				break;
	
			// Northeast
			case 6:
				$catId = 5;
				break;
	
			// Northwest & West Coast
			case 7:
				$catId = 5;
				break;
	
			// Southeast
			case 8:
				$catId = 5;
				break;
	
			// Midwest
			case 9:
				$catId = 5;
				break;
	
			// Southwest
			case 10:
				$catId = 5;
				break;
	
			// Swinging 'round the Globe
			case 11:
				$catId = 5;
				break;
	
			// Support
			case 12:
				$catId = 31;
				break;
	
			// The Staff Speakeasy
			case 13:
				$catId = 32;
				break;
	
			// National
			case 15:
				$catId = 5;
				break;
	
			// The Welcome Wagon
			case 17:
				$catId = 27;
				break;
	
			// Dead Letter Office
			case 18:
				$catId = 34;
				break;
	
			// News
			case 19:
				$catId = 1;
				break;
	
			// Politics
			case 20:
				$catId = 28;
				break;
	
			// New York City
			case 21:
				$catId = 5;
				break;
	
			// Frankie Autobiography News & Info
			case 22:
				$catId = 14;
				break;
	
			// Yehoodi's 10th Anniversary Party!
			case 23:
				$catId = 1;
				break;
				
			// Frankie 95
			case 24:
				$catId = 14;
				break;
				
			default:
			// Temporary storage place for everything else
				$catId = 34;
				break;
		}

		$lastCommentId = 0;
		$title = trim(htmlspecialchars($value['topic_title']));
		$bbcode_uid = $value['bbcode_uid'];
		$views = $value['topic_views'];
		
		// Check if user 4823 Yehoodi News is the poster
		// And use the descrption from the news instead of
		// the topic description
		if($userId == 4823) {
			if($newsObj->getSingleNewsArticle($value['topic_title'])) {
				// Get the other values like normal
				$descrip = $postObj->getSinglePost($value['topic_id']);
				$rsrcDate = date("Y-m-d", $descrip['post_time'] );
				
				$postText = $newsObj->getSingleNewsArticle($value['topic_title']);
				$postText = trim(htmlspecialchars($postText));
			} else {
				$descrip = $postObj->getSinglePost($value['topic_id']);
				$postText = trim(htmlspecialchars($descrip['post_text']));
				$rsrcDate = date("Y-m-d", $descrip['post_time'] );
			}
		} else {
			$descrip = $postObj->getSinglePost($value['topic_id']);
			$postText = trim(htmlspecialchars($descrip['post_text']));
			$rsrcDate = date("Y-m-d", $descrip['post_time'] );
		}
		
		// This date blows up the RelativeDate function so for now, set all invalid dates to my birthday in 2000
		if ($rsrcDate == "1969-12-31 00:00:00")
			$rsrcDate == "2000-01-04 00:00:00";
			
		//print_r($value['$topic_id']);die;
		switch ($value['topic_type']) {
			case 0:
				$sticky = 0;
				break;
				
			case 1:
				$sticky = 1;
				break;
				
			case 2:
				$sticky = 0;
				break;
		}
		
		$closed = $value['topic_status'];
		
		//$postText = str_replace(':'.$bbcode_uid,'',$postText);
	
	$sql .= "INSERT INTO resource SET rsrc_id = {$topicId}, user_id = {$userId}, cat_id = {$catId}, last_comment_id = {$lastCommentId}, title = \"{$title}\", descrip = \"{$postText}\", rsrc_date = \"{$rsrcDate}\", closed = {$closed}, bbcode_uid = \"{$bbcode_uid}\", sticky = {$sticky}, is_active = 1;\n";
	}
unset($phpbbTopicData);
}

//print_r($output);
//echo $sql;

file_put_contents($newFile, $sql);
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";
