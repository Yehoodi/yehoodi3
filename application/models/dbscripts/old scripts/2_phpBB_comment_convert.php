<?php

/**
 * Easier now that the RegEx is in here.
 * 
 */

ini_set("memory_limit", "64M");
require('phpBB.class.php');
require('Yehoodi3.class.php');
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

// Topics become Chat Resources
/**
 * topic_status 1 = locked thread
 * topic_type 1 = Sticky
 * 			  2 = Announcement
 * 
 */

/**
 * Encode an IP address to a MySQL
 * storeable string
 *
 * @param unknown_type $dotquad_ip
 * @return unknown
 */
function encode_ip($dotquad_ip)
{
	$ip_sep = explode('.', $dotquad_ip);
	return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

/**
 * Decode a MySQL stored string
 * back into an IP address
 *
 * @param unknown_type $int_ip
 * @return unknown
 */
function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

// Clean the comments with this regex
$filters = array(
    // replace non-alphanumeric characters with nothing
    '/[^a-z0-9.,?&\-():;!=_\'\[\]\/\n]+/i' => ' ',

    // replace multiple spaces with a single space
    '/ +/'          => ' '
);

$newFile = 'phpBBCommentImport.sql';
//$error_log = 'comment_errors.txt';

$file = fopen($newFile, 'w');
//$errors = fopen($error_log,'w');

// Objects
$commentObj = new PhpBB();
$postObj = new Yehoodi3();

// Vars
//$comment = $commentObj->getCommentContentsById(113);
$totalPosts = $commentObj->getTopicCount();
//$totalPosts = 100;
$totalComments = $commentObj->getCommentCount();
$limit = 1000;

//var_dump($postObj->getResources());die;
// Here we go!
$timer->start();
echo "Migrating {$totalComments} comments into {$totalPosts} posts!\n\n";
for ($offset = 0; $offset <= $totalPosts; $offset += $limit) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset." of {$totalPosts}.\n";
	$phpbbPostData = $postObj->getResources($limit, $offset);


	foreach ($phpbbPostData as $value) {
		
		echo ".";
		$rsrcId = $value['rsrc_id'];
		$userId = $value['user_id'];
		$commentCount = $value['count_comments'];	

		$commentCount = $commentObj->getCommentCountById($rsrcId);
		$commentData = $commentObj->getCommentContentsById($rsrcId,$commentCount);
		
		$counter = 1;
		foreach ($commentData as $v) {
			//print_r($v);
			$commentId = $v['post_id'];
			$resourceId = $rsrcId;
			$userId = $v['poster_id'];
			$commentNum = $counter;
			
			$comment = trim(htmlspecialchars(stripslashes($v['post_text'])));

			// apply each replacement
			foreach ($filters as $regex => $replacement)
			    $comment = preg_replace($regex, $replacement, $comment);

			//$replyToId = $rsrcId;
			$replyToId = 0;
			$dateCreated = date("Y-m-d H:i:s", $v['post_time'] );
			
			if($counter == $rowCount) {
				$dateLastActive = $dateCreated;
			} else {
				$dateLastActive = "0000-00-00 00:00:00";
			}
			$isActive = 1;
			$bbcode_uid = $v['bbcode_uid'];
			$remoteIP = decode_ip($v['poster_ip']);
			
			$counter++;
			
			// strip bbcode uids	
			//$comment = str_replace(':'.$bbcode_uid,'',$comment);
			
			// Insert statement!!
			$sql .= "INSERT INTO comment SET comment_id = {$commentId}, rsrc_id = {$resourceId}, user_id = {$userId}, comment_num = {$commentNum}, comment = \"{$comment}\", reply_to_id = {$replyToId}, date_created = \"{$dateCreated}\", date_last_active = \"{$dateLastActive}\", is_active = {$isActive}, bbcode_uid = \"{$bbcode_uid}\", remote_ip = INET_ATON('{$remoteIP}');\n";
		}
		fwrite($file,$sql);
		$sql = "";
	}
}

fclose($file);
echo "\nDone!";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.";