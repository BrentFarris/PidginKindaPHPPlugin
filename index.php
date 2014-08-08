<?php

define('PATH', realpath(dirname(__FILE__)) . '/');
define('SETTINGS', PATH . 'settings.js');

$settings = json_decode(file_get_contents(SETTINGS), true);

function ToMonospace($str) {
	return '<font face="Courier 10 Pitch" size="1">' . $str . '</font>';
}

function DONE() {
	$file = PATH . FILE_LOC;
	if (!is_dir($file) && !empty($file)) {
		file_put_contents($file, time());
	}
	
	exit;
}

function Send($msg, $user='', $mono=false) {
	if ($mono)
		$msg = ToMonospace(htmlentities($msg));
	else
		$msg = htmlentities($msg);
	
	// TODO:  Change this to do a lookup with assoc table
	if (strpos($msg, '__DEG__') !== false)
		$msg = str_replace('__DEG__', '&#176;', $msg);
	
	if (empty($user))
		echo '<b><font color="2D4287">'.$msg.'</font></b>';
	else
		echo '<b><font color="3A872D">'.$user.':</font> <font color="2D4287">'.$msg.'</font></b>';
}

$cmdChar = '\\';
//$me = $argv[1];
$me = $_GET['me'];
//$sender = $argv[2];
$sender = $_GET['sender'];
//$message = $argv[3];			// Add space at end of string to check multiple commands
$message = $_GET['msg'] . ' ';	// that may start with the same string of characters

if (strpos($sender, '[') === 0 && strpos($message, '(') === 0) {
	$sender = trim(substr($message, 1, strpos($message, ')') - 1));
	$message = trim(substr($message, strpos($message, ')') + 1));
}

$file = '';
if (!empty($sender))
	$file = 'Users/' . str_replace('/', '', $sender);

define('FILE_LOC', $file);

if (file_exists(PATH . $file)) {
	$last = file_get_contents(PATH . $file);

	if ($last >= time())
		DONE();
}

$primary = array($cmdChar.'off ', $cmdChar.'on ');

if (in_array($message, $primary)) {
	if ($sender == $me) {
		if ($message == $cmdChar.'off ') {
			$settings['active'] = false;
			Send('Turning off Subconscious');
			file_put_contents(SETTINGS, json_encode($settings));
			DONE();
		} else if ($message == $cmdChar.'on ') {
			$settings['active'] = true;
			Send('Subconscious turned on');
			file_put_contents(SETTINGS, json_encode($settings));
			DONE();
		}
	} else {
		Send('Computer says no... D:').
		DONE();
	}
}

if (!$settings['active'])
	DONE();

define('TIME_FORMAT', 'M d Y h:i:s a');
define('GEO_CODE', '56738275');

$cmd = explode(' ', $message);

if (count($cmd) < 1)
	return;

$arg = count($cmd) > 1 ? $cmd[1] : '';
$cmd = $cmd[0];

$methods = array(
	'roll' => function($sender, $arg) { $rolls = 'rolled ' . mt_rand(1, 6); if (is_numeric($arg) && $arg > 0 && $arg <= 6) { for ($i = 0; $i < $arg - 1; $i++) $rolls .= ', ' . mt_rand(1, 6); } else if ($arg < 0) { $rolls = ", guess what happens when you roll negative dice... you get a 0. Congratulations, you lose"; } else if ($arg > 6) { $rolls = ", I only have 6 dice D:"; } Send($rolls, $sender); },
	'd20' => function($sender, $arg) { $rolls = 'rolled ' . mt_rand(1, 20); if (is_numeric($arg) && $arg > 0 && $arg <= 6) { for ($i = 0; $i < $arg - 1; $i++) $rolls .= ', ' . mt_rand(1, 20); } else if ($arg < 0) { $rolls = ", guess what happens when you roll negative dice... you get a 0. Congratulations, you lose"; } else if ($arg > 6) { $rolls = ", I only have 6 dice D:"; } Send($rolls, $sender); },
	/*'brent' => function() { $lines = explode("\n", trim(file_get_contents(PATH . 'brent.txt'))); shuffle($lines); Send($lines[0], '', true); },*/
	'gmt' => function() { Send(gmdate(TIME_FORMAT, time())); },
	'time' => function() { Send(date(TIME_FORMAT, time())); },
	'epoch' => function() { Send(time()); },
	'togmt' => function($sender, $arg) { if (!empty($arg) && is_numeric($arg)) Send(gmdate(TIME_FORMAT, $arg)); else Send('You must pass an epoch time argument, example: \\togmt [integer]'); /* 651144633 */ },
	'totime' => function($sender, $arg) { if (!empty($arg) && is_numeric($arg)) Send(date(TIME_FORMAT, $arg)); else Send('You must pass an epoch time argument, example: \\totime [integer]'); /* 651169833 */ },
	'weather' => function() { $xml = simplexml_load_file('http://weather.yahooapis.com/forecastrss?w=' . GEO_CODE); $current = $xml->channel->item->description; $start = strpos($current, 'Current Conditions:') + strlen('Current Conditions:</b><br />'); $end = strpos($current, '<BR />', $start); Send(trim(str_replace(' F', '__DEG__ F', substr($current, $start, $end - $start)))); },
	'md5' => function($sender, $arg) { Send(md5($arg)); },
	'coin' => function($sender, $arg) { $rand = mt_rand(0, 1); if (!empty($arg)) Send('asked their '.$arg.' and got a '.($rand ? 'YES' : 'NO'), $sender); else Send('fliped a coin and got '.($rand ? 'HEADS' : 'TAILS'), $sender); },
	'troll' => function($sender, $arg) use(&$settings, $me) { if ($arg == 'off' || empty($arg)) { $settings['troll'] = ''; } else if($arg == $me) { Send("HAHA! You can't troll this!"); } else { $settings['troll'] = $sender.'|'.$arg; Send('wishes to troll ' . $arg, $sender); } }
);

if (strpos($cmd, $cmdChar) === 0) {
	$cmd = ltrim($cmd, $cmdChar);
	
	if (array_key_exists($cmd, $methods))
		$methods[$cmd]($sender, $arg);
	else if ($cmd == 'help')
		Send('Commands: \\' . implode(', \\', array_keys($methods)));
} else {
	if (strpos($settings['troll'], '|') !== false) {
		$troll = explode('|', $settings['troll']);
		if ($sender == $troll[1]) {
			$quiets = array('shhhh', 'hey '.$troll[1].', the big kids are talking right now', '... so anyway', 'ugh', "hmm? I'm sorry, were you saying something?", "You know what I don't like? People with the username ".$troll[1], 'psh '.$troll[1].' ... what kind of username is that?', 'This is what not caring looks like', strtoupper(trim($message)).'!');
			
			shuffle($quiets);
			
			Send($quiets[0], $troll[0]);
		}
	} else if ($sender != $me) {
		$lols = array('lol', 'haha', 'hehe');
		foreach ($lols as $lol) {
			if (strpos(strtolower($message), $lol) !== false) {
				Send('lolololol');
				break;
			}
		}
	}
}

file_put_contents(SETTINGS, json_encode($settings));
DONE();
