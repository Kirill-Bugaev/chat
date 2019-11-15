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

if ( isset($_POST['last_msg_id']) && ($last_msg_id = trim($_POST['last_msg_id'])) != "" )
	$last_msg_id = mysqli_real_escape_string($dbconn, $last_msg_id);
else
	xml_die("Last message id should be specified");

check_token($dbconn, $name, $token);
update_user_activity($dbconn, $name);

// Get online users
$query = "SELECT name FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL $user_conn_timeout SECOND) ORDER BY name";
$users_result = $dbconn->query($query);
check_query_result($dbconn, $users_result);

// Get new messages
$query = "SELECT * FROM messages WHERE id > '$last_msg_id' ORDER BY id;";
$messages_result = $dbconn->query($query);
check_query_result($dbconn, $messages_result);

// Response
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "<xml>";

// Users
echo '<users>';
$rows = $users_result->num_rows;
for ($i = 0; $i < $rows; $i++) {
	$row = $users_result->fetch_array(MYSQLI_ASSOC);
	echo '<user>' . $row['name'] . '</user>';
}
echo '</users>';
$users_result->close();

// Messages
echo '<messages>';
$rows = $messages_result->num_rows;
for ($i = 0; $i < $rows; $i++) {
	$row = $messages_result->fetch_array(MYSQLI_ASSOC);
	echo '<msg'
		. ' id="'    . $row['id']                      . '"'
		. ' time="'  . $row['time']                    . '"'
		. ' uname="' . htmlentities($row['user_name']) . '"'
		. '>'        . htmlentities($row['text'])
		. '</msg>';
}
echo '</messages>';
$messages_result->close();

echo "</xml>";

$dbconn->close();

?>
