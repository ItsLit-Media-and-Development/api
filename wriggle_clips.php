<?php
/**
 * Wriggles Clip Page
 *
 * Output 100 wrigglemania clips
 *
 * @package        API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2017 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 0.1
 * @filesource
 */
error_reporting(E_ALL);

$json = '';
$data = '';

if(isset($_GET['page'])) {
	$json = file_get_contents('https://api.itslit.uk/clips/get_clips/wrigglemania/100/all/' . $_GET['page']);
} else {
	$json = file_get_contents('https://api.itslit.uk/clips/get_clips/wrigglemania');
	$data = json_decode($json, true);
}
$data = json_decode($json, true);
?>
    <html>
    <head>
        <title>Wrigglemania Clip list</title>
    </head>
    <body>
<?php

if(is_array($data['response'])) {
	$count = count($data['response']['clips']);
	echo "<table>";
	echo "<tr><td>Clips retrieved: " . $count . "</td></tr>";

	$output = $data['response']['clips'];

	for($i = 0; $i < $count; $i++) {
		echo "<tr>";
		echo '<td><a href="https://clips.twitch.tv/' . $output[$i]['slug'] . '" target="_blank">https://clips.twitch.tv/' . $output[$i]['slug'] . '</a></td><td> Created by <a href="' . $output[$i]['curator']['channel_url'] . '" target="_blank">' . $output[$i]['curator']['name'] . '</a></td>
<td> on ' . $output[$i]['created_at'] . ' whilst playing ' . $output[$i]['game'] . '</td>';
		echo "</tr>";
	}

	if($data['response']['_cursor'] != '') {
		echo "<tr></tr>";
		echo "<tr></tr>";
		echo "<tr><td><a href='https://marctowler.co.uk/wriggle_clips.php?page=" . $data['response']['_cursor'] . "'>Next Page ></a> </td></tr>";
		echo "</table>";
	}
}
