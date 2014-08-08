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
$file = 'Users/' . str_replace('[', '', str_replace(']', '', str_replace('/', '', $sender)));
//$message = $argv[3];			// Add space at end of string to check multiple commands
$message = $_GET['msg'] . ' ';	// that may start with the same string of characters

if (strpos($sender, '[') === 0 && strpos($message, '(') === 0) {
	$sender = trim(substr($message, 1, strpos($message, ')') - 1));
	$message = trim(substr($message, strpos($message, ')') + 1));
}

define('PATH', realpath(dirname(__FILE__)) . '/');

if (file_exists(PATH . $file)) {
	$last = file_get_contents(PATH . $file);

	if ($last >= time())
		exit;
}

define('TIME_FORMAT', 'M d Y h:i:s a');

$cmd = explode(' ', $message);

if (count($cmd) < 1)
	return;

$arg = count($cmd) > 1 ? $cmd[1] : '';
$cmd = $cmd[0];

$methods = array(
	'roll' => function($sender, $arg) {
		$rolls = 'rolled ' . mt_rand(1, 6);
		if (is_numeric($arg) && $arg > 0 && $arg < 6) {
			for ($i = 0; $i < $arg - 1; $i++)
				$rolls .= ', ' . mt_rand(1, 6);
		}
		
		Send($rolls, $sender);
	},
	'd20' => function($sender, $arg) {
		$rolls = 'rolled ' . mt_rand(1, 20);
		if (is_numeric($arg) && $arg > 0 && $arg < 6) {
			for ($i = 0; $i < $arg - 1; $i++)
				$rolls .= ', ' . mt_rand(1, 20);
		}
		
		Send($rolls, $sender);
	},
	/*'brent' => function()
	{
		$lines = explode("\n", trim(file_get_contents(PATH . 'brent.txt')));
		shuffle($lines);
		Send($lines[0], '', true);
	},*/
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
		Send('Commands: \\' . implode(', \\', array_keys($methods)));
} else {
	$lols = array('lol', 'haha', 'hehe');
	if ($sender != $me) {
		foreach ($lols as $lol) {
			if (strpos(strtolower($message), $lol) !== false) {
				echo 'lolololol';
				break;
			}
		}
	}
}

file_put_contents(PATH . $file, time());
