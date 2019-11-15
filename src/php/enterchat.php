<?php

require_once 'login.php';
require_once 'config.php';
require_once 'routine.php';

function gen_token() {
	$token = "";
	for ($i = 0; $i < 16; $i++)
		$token .= chr(rand(65, 90));
	return $token;
}

header('Content-Type: text/xml');

$dbconn = db_connect($hostname, $username, $password, $database);

if ( isset($_POST['name']) && ($name = trim($_POST['name'])) != "" )
	$name = mysqli_real_escape_string($dbconn, $name);
else
	xml_die("User name should be specified");

$query = "SELECT last_activity FROM users WHERE name = '$name';";
$result = $dbconn->query($query);
check_query_result($dbconn, $result);

if ($result->num_rows == 0) {
	$result->close();
	$token = gen_token();
	$query = "INSERT INTO users(name, token) VALUES('$name', '$token');";
	$result = $dbconn->query($query);
	check_query_result($dbconn, $result);
} else {
	$db_time = get_db_time($dbconn);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	if (strtotime($db_time) - strtotime($row['last_activity']) < $user_conn_timeout)
		xml_die("User name busy");
	$result->close();
	$token = gen_token();
	$query = "UPDATE users SET token = '$token' WHERE name = '$name';";
	$result = $dbconn->query($query);
	check_query_result($dbconn, $result);
}

$dbconn->close();

echo <<<_END
<?xml version="1.0" encoding="UTF-8"?>
<xml>
	<token>$token</token>
</xml>
_END;

?>
