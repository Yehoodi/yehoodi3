<?php
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

require('phpBB.class.php');
require('Yehoodi3.class.php');

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

$newFile = 'phpBBPrivMessageImportV2.sql';

$file = fopen($newFile, 'w');

// Objects
$privMsgObj = new PhpBB();
$userObj = new Yehoodi3();

// Vars
$totalUsers = $userObj->getUserCount();
$mostEmailUserTracker = 0;

// Here we go!
$timer->start();
echo "Migrating {$totalUsers} private messages into Yehoodi 3!\n\n";
	
$userId = $userObj->getUserIds();

	foreach ($userId as $value) {
		
		// Get the user's messages
		$userMessages = $privMsgObj->getAllMessagesByUserId($value['user_id']);
		echo "Converting user: " . $value['user_name'] . "\n";
		
		foreach ($userMessages as $v) {
			$id = $v['privmsgs_id'];
			$userFrom = $v['privmsgs_from_userid'];
			$userTo = $v['privmsgs_to_userid'];
			$subject = cleanText($v['privmsgs_subject']);
			$subject = str_replace('Re: ','',$subject);
			$messageDate = date("Y-m-d G:i:s", $v['privmsgs_date'] );
			$remoteIP = decode_ip($v['privmsgs_ip']);
			
			$sql .= "INSERT IGNORE INTO temp_mail SET mail_id = {$id}, user_id_from = {$userFrom}, user_id_to = {$userTo}, mail_subject = \"{$subject}\", mail_date = \"{$messageDate}\", remote_ip = INET_ATON('{$remoteIP}');\n";
		}
		
		if (count($userMessages) > $mostEmailUserTracker ) {
			$mostEmailUserTracker = count($userMessages);
			$mostEmailUser = $value;
		}
		
		fwrite($file,$sql);
		$sql = "";
	}

fclose($file);
echo "\nDone!";
$timer->stop();
echo "\nElapsed time was: " . round($timer->timeElapsed(),2) ." seconds.\n";
echo "The user with the most email is: " . $mostEmailUser['user_name'] . " (UserID:" . $mostEmailUser['user_id'] . ").\n";