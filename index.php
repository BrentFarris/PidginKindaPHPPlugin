<?php

function ToMonospace($str) {
	return '<font face="Courier 10 Pitch" size="1">' . $str . '</font>';
}

function Send($msg, $user='', $mono=false) {
	if ($mono)
		$msg = ToMonospace(htmlentities($msg));
	else
		$msg = htmlentities($msg);
	
	if (empty($user))
		echo $msg;
	else
		echo $user . ' ' . $msg;
}

$cmdChar = '\\';
$sender = $_GET['sender'];
$file = 'Users/' . $sender;
$message = $_GET['msg'] . ' ';	// Add space at end of string to check multiple commands
								// that may start with the same string of characters

if (file_exists($file)) {
	$last = file_get_contents($file);

	if ($last >= time())
		exit;
}

file_put_contents($file, time());

define('TIME_FORMAT', 'M d Y h:i:s a');

$cmd = explode(' ', $message);
if (count($cmd) < 1)
	return;

$arg = count($cmd) > 1 ? $cmd[1] : '';
$cmd = $cmd[0];

$methods = array(
	'roll' => function($sender) { Send('rolled ' . mt_rand(1, 6), $sender); },
	'brent' => function() { Send(file_get_contents('brent.txt'), '', true); },
	'gmt' => function() { Send(gmdate(TIME_FORMAT, time())); },
	'epoch' => function() { Send(time()); },
	'togmt' => function($sender, $arg) { Send(gmdate(TIME_FORMAT, $arg)); },
	'totime' => function($sender, $arg) { Send(date(TIME_FORMAT, $arg)); }
);

if (strpos($cmd, $cmdChar) === 0) {
	$cmd = ltrim($cmd, $cmdChar);
	
	if (array_key_exists($cmd, $methods))
		$methods[$cmd]($sender, $arg);
}
