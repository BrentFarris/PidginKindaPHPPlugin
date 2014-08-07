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
//$me = $argv[1];
$me = $_GET['me'];
//$sender = $argv[2];
$sender = $_GET['sender'];
$file = 'Users/' . rtrim($sender, '/');
//$message = $argv[3];			// Add space at end of string to check multiple commands
$message = $_GET['msg'] . ' ';	// that may start with the same string of characters

define('PATH', realpath(dirname(__FILE__)) . '/');

if (file_exists(PATH . $file)) {
	$last = file_get_contents(PATH . $file);

	if ($last >= time())
		exit;
}

file_put_contents(PATH . $file, time());

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
	'time' => function() { Send(date(TIME_FORMAT, time())); },
	'epoch' => function() { Send(time()); },
	'togmt' => function($sender, $arg) { if (!empty($arg)) Send(gmdate(TIME_FORMAT, $arg)); else Send('You must pass an epoch time argument, example: \\togmt [integer]'); /* 651144633 */ },
	'totime' => function($sender, $arg) { if (!empty($arg)) Send(date(TIME_FORMAT, $arg)); else Send('You must pass an epoch time argument, example: \\totime [integer]'); /* 651169833 */ }
);

if (strpos($cmd, $cmdChar) === 0) {
	$cmd = ltrim($cmd, $cmdChar);
	
	if (array_key_exists($cmd, $methods))
		$methods[$cmd]($sender, $arg);
	else if ($cmd == 'help')
		Send('\\' . implode(', \\', array_keys($methods)));
} else {
	if ($sender != $me) {
		if (strpos($message, 'lol') !== false || strpos($message, 'haha') !== false)
			echo 'lolololol';
	}
}
