<?php

function xml_die($msg) {
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo "<xml>";
	echo "<error>$msg</error>";
	echo "</xml>";
	die();
}

function db_connect($hn, $un, $pw, $db) {
	$dbconn = new mysqli($hn, $un, $pw, $db);
	if ($dbconn->connect_error) {
		xml_die("MySQL connection error. " . $dbconn->connect_error);
	}
	return $dbconn;
}

function check_query_result($dbconn, $result) {
	if (!$result) {
		xml_die($dbconn->error);
	}
}

function check_token($dbconn, $name, $token) {
	$query = "SELECT name FROM users WHERE name = '$name' AND token = '$token';";
	$result = $dbconn->query($query);
	check_query_result($dbconn, $result);
	if ($result->num_rows == 0)
		xml_die("Invalid user name or token");
	$result->close();
}

function update_user_activity($dbconn, $name) {
	$cur_datetime = date("Y-m-d H:i:s");
	$query = "UPDATE users SET last_activity = '$cur_datetime' WHERE name = '$name';";
	$result = $dbconn->query($query);
	check_query_result($dbconn, $result);
}
?>
