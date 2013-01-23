<?php
require('Benchmark'.DIRECTORY_SEPARATOR.'Timer.php');
$timer = new Benchmark_Timer();

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


$newFile = 'mailMessageImport.sql';

$file = fopen($newFile, 'w');

// Objects
$userObj = new Yehoodi3();

// Vars
$totalUsers = $userObj->getUserCount();
$subject= '';
$counter = 0;
//$mostEmailUserTracker = 0;

// Here we go!
$timer->start();
echo "Creating thread_id's for Yehoodi Mail import!\n\n";
	
$userId = $userObj->getUserIds();
/*$userId = array(array('user_name' => 'spuds',
				'user_id' 	=> '1762'),
				array('user_name' => 'Eff',
				'user_id' 	=> '1512'));
*/
	foreach ($userId as $value) {
		
		// Get the user's messages
		$userMessages = $userObj->getAllMessagesByUserIdSecondPass($value['user_id']);
		echo "Converting user: " . $value['user_name'] . "\n";
		
		foreach ($userMessages as $val) {

			if ($subjectText == $val['mail_subject']) {
				// nothing
			} else {
				$counter++;
			}

			$id = $val['mail_id'];
			$userFrom = $val['user_id_from'];
			$userTo = $val['user_id_to'];
			$subject = $val['mail_subject'];
			$messageDate = $val['mail_date'];
			$remoteIP = $val['remote_ip'];
			$threadId = $counter;

			$sql .= "INSERT IGNORE INTO mail SET mail_id = {$id}, thread_id = {$counter}, user_id_from = {$userFrom}, user_id_to = {$userTo}, mail_subject = \"{$subject}\", mail_date = \"{$messageDate}\", remote_ip = INET_ATON('{$remoteIP}');\n";

			$subjectText = $val['mail_subject'];

		}
		
		fwrite($file,$sql);
		$sql = "";
	}

fclose($file);
echo "\nDone!";
$timer->stop();
$seconds = round($timer->timeElapsed(),2);
if ($seconds < 60) {
	echo "Elapsed time was: " . round($timer->timeElapsed(),2) ." seconds.\n";
} else {
	echo "Elapsed time was: " . round($seconds / 60, 2) ." minutes.\n";
}
