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

function StartsWith($haystack, $needle) {
	return strpos($haystack, '/' . $needle . ' ') === 0;
}

$sender = $_GET['sender'];
$message = $_GET['msg'] . ' ';	// Add space at end of string to check multiple commands
								// that may start with the same string of characters

$cmd = explode(' ', $message);
$cmd = $cmd[0];

$methods = array(
	'/roll' => function($sender) { Send('rolled ' . mt_rand(1, 6), $sender); },
	'/brent' => function($sender) { Send(file_get_contents('brent.txt'), '', true); }
);

if (array_key_exists($cmd, $methods))
	$methods[$cmd]($sender);
