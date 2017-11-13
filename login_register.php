<!DOCTYPE HTML>
<html>
<head>
<style>
</style>
</head>

<body>

<?php
/*
login_register.php

Page for useres to either login to an existing account or 
registering a new account for the Password manager.

Valid username and password needs to be input for 
redirection to password_storing.php else, it will redirect back
to this page telling the user to input valid info.
*/

// session_start();
include_once 'db_connect.php';

$username = "";
$password = "";

$username_error	= "";
$password_error	= "";

$sql = "";

// check if username already exists in database
$username_exist = mysqli_query("SELECT username FROM users WHERE 'username' = '$username'");
if($username_exist && mysqli_num_rows($username_exist) > 0)
{
	// username already exists
	
}
else
{
	// insert username and password into database
	$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
	if($connection->query($sql) === FALSE)
	{
		// error inserting username and password
		echo "Error: " . $sql . "<br>" . $connection->error;
	}
}


?>