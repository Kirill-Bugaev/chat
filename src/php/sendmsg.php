<?php

require_once 'login.php';
require_once 'config.php';
require_once 'routine.php';

header('Content-Type: text/xml');

$dbconn = db_connect($hostname, $username, $password, $database);

if ( isset($_POST['name']) && ($name = trim($_POST['name'])) != "" )
	$name = mysqli_real_escape_string($dbconn, $name);
else
	xml_die("User name should be specified");

if ( isset($_POST['token']) && ($token = trim($_POST['token'])) != "" )
	$token = mysqli_real_escape_string($dbconn, $token);
else
	xml_die("Token should be specified");

if ( isset($_POST['text']) && ($text = trim($_POST['text'])) != "" )
	$text = mysqli_real_escape_string($dbconn, $text);
else
	xml_die("Message text should be specified");

check_token($dbconn, $name, $token);
update_user_activity($dbconn, $name);

$query = "INSERT INTO messages(user_name, text) VALUES ('$name', '$text');";
$result = $dbconn->query($query);
check_query_result($dbconn, $result);

$dbconn->close();

echo <<<_END
<?xml version="1.0" encoding="UTF-8"?>
<xml>
</xml>
_END;

?>
