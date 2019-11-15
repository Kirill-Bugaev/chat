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

function get_db_time($dbconn) {
	$query = "SELECT NOW();";
	$result = $dbconn->query($query);
	check_query_result($dbconn, $result);
	$row = $result->fetch_array(MYSQLI_NUM);
	$now = $row[0];
	$result->close();
	return $now;
}

function update_user_activity($dbconn, $name) {
	// stub query, MySQL updates last_activity automatically when updating row in this table
	$query = "UPDATE users SET last_activity = NOW() WHERE name = '$name';";
	$result = $dbconn->query($query);
	check_query_result($dbconn, $result);
}

?>
