<?php
// try to connect to database on xampp localhost
// using these parameters

$servername = "localhost";
$username 	= "root";
$password	= "";
$dbname		= "simple password manager";


$connection = mysqli_connect($servername, $username, $password, $dbname);

if($connection->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

?>