<?php

function ToMonospace($str) {
	return '<font face="Courier 10 Pitch" size="1">' . $str . '</font>';
}

function Send($msg, $user='') {
	if (empty($user))
		echo htmlentities($msg);
	else
		echo $user . ' ' . htmlentities($msg);
}

$sender = $_GET['sender'];
$message = $_GET['msg'];

if ($message == '/roll')
	Send('rolled ' . mt_rand(1, 6), $sender);
else if ($message == "/brent")
	Send(ToMonospace(htmlentities(file_get_contents('brent.txt'))));
