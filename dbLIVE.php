<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "madni6g7_jivandhara";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

?>
