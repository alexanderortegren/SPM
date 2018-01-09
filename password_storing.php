<?php
/*
password_storing.php

Page for storing the passwords given by the user or 
generated on this page.


*/
?>

<!DOCTYPE HTML>
<html>
<head>
<style>
</style>
</head>

<body>

<?php
session_start();
if(empty($_SESSION['username'])) {
    header("Location: login_register.php");
}
include_once 'db_connect.php';

$session_username = $_SESSION['username'];

$site_name_store = "";
$site_password_store = "";

$site_name_store_error = "";
$site_password_store_error = "";

$site_name_get = "";
$site_password_get = "";

$site_name_get_error = "";
$site_password_get_error = "";

$retrieved_password = "";
$generated_password = "";

$store_error = FALSE;
$get_error = FALSE;

$sql = "";
$sitename_exist = "";
$hash = "";
$salt = "";

$uppercase = "";
$lowercase = "";
$number = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(isset($_POST["logout-btn"]))
	{
		unset($_SESSION['username']);
		session_destroy();
		header("Location: login_register.php");
	}

	if(isset($_POST["store_password-btn"]))
	{
		if(empty($_POST["site_name_store"]))
		{
			$site_name_store_error = "Site name required";
			$store_error = TRUE;
		}
		else
		{
			$store_error = FALSE;
			$site_name_store = test_input($_POST["site_name_store"]);
			if(strlen($site_name_store) > 150)
			{
				$store_error = TRUE;
				$site_name_store_error = "Site name too long (max 150 chars)";
			}
		}
		
		if(empty($_POST["site_password_store"]))
		{
			$site_password_store_error = "Site password required";
			$store_error = TRUE;
		}
		else
		{
			$store_error = FALSE;
			$site_password_store = test_input($_POST["site_password_store"]);
			
			$uppercase = preg_match('@[A-Z]@', $site_password_store);
			$lowercase = preg_match('@[a-z]@', $site_password_store);
			$number    = preg_match('@[0-9]@', $site_password_store);
			
			if(!$uppercase || !$lowercase || !$number)
			{
				$site_password_store_error = "Password needs at least 1 uppercase, 1 lowercase, and 1 number";
				$store_error = TRUE;
			}
			if(strlen($site_password_store) < 8)
			{
				$store_error = TRUE;
				$site_password_store_error = "Site password size: 8 char minimum";
			}
			if(strlen($site_password_store) > 150)
			{
				$store_error = TRUE;
				$site_password_store_error = "Site password size: 150 char maximum";
			}
		}
		
		
		// if no errors in input detected when storing
		// insert entries into database 
		if($store_error == FALSE)
		{	
			$sitename_exist = mysqli_query($connection, "SELECT sitename FROM sites WHERE username = '$session_username' AND sitename = '$site_name_store'");
			
			if($sitename_exist && (mysqli_num_rows($sitename_exist) > 0))
			{
				echo "Updated password for '$site_name_store'";
				
				// encrypt site_password_store for storage in database
				$site_password_store = my_simple_crypt($site_password_store, $session_username, strrev($session_username), 'e');
				
				// sitename exists
				// update old password with new
				$sql = "UPDATE sites SET sitepassword = '$site_password_store' WHERE username = '$session_username' AND sitename = '$site_name_store'";
				if($connection->query($sql) === FALSE)
				{
					// error updating password
					echo "Error: " . $sql . "<br>" . $connection->error;
				}
			}
			else
			{
				echo "Inserted password for '$site_name_store'";
				// echo "Inserted password for  '$session_username', '$site_name_store', '$site_password_store'";
				
				// encrypt site_password_store for storage in database
				$site_password_store = my_simple_crypt($site_password_store, $session_username, strrev($session_username), 'e');
				
				$sql = "INSERT INTO sites (username, sitename, sitepassword) VALUES ('$session_username', '$site_name_store', '$site_password_store')";
				if($connection->query($sql) === FALSE)
				{
					// error inserting username, sitename and password
					echo "Error: " . $sql . "<br>" . $connection->error;
				}
			}		
		}	
	}
	
	if(isset($_POST["get_password-btn"]))
	{
		if(empty($_POST["site_name_get"]))
		{
			$site_name_get_error = "Site name required";
			$get_error = TRUE;
		}
		else
		{
			$get_error = FALSE;
			$site_name_get = test_input($_POST["site_name_get"]);
			if(strlen($site_name_get) > 150)
			{
				$get_error = TRUE;
				$site_name_get_error = "Site name too long (max 150 chars)";
			}
		}
		
		if($get_error == FALSE)
		{		
			$sitename_exist = mysqli_query($connection, "SELECT sitename FROM sites WHERE username = '$session_username' AND sitename = '$site_name_get'");
			
			if($sitename_exist && (mysqli_num_rows($sitename_exist) > 0))
			{
				// sitename exists for that user
				// display the password
				$sql = mysqli_query($connection, "SELECT sitepassword FROM sites WHERE username = '$session_username' AND sitename = '$site_name_get'");				
				
				if($site_password_get = $sql->fetch_assoc())
				{
					$retrieved_password = $site_password_get['sitepassword'];
					//decrypt password from database
					$retrieved_password = "password: " . my_simple_crypt($retrieved_password, $session_username, strrev($session_username), 'd');
				}			
			}
			else
			{
				// sitename was not found in db
				echo "sitename was not found in db";	
			}							
		}	
	}
	
	if(isset($_POST["generate_password-btn"]))
	{
		$generated_password = random_password();//generate_password(random_password(), $session_username);
	}
	
	
}

// https://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
function my_simple_crypt($string, $secret_key, $secret_iv, $action = 'e') 
{
	// you may change these values to your own
	//$secret_key = 'my_simple_secret_key';
	//$secret_iv = 'my_simple_secret_iv';

	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash( 'sha512', $secret_key );
	$iv = substr( hash( 'sha512', $secret_iv ), 0, 16 );

	if( $action == 'e' ) {
		$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	}
	else if( $action == 'd' ){
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}

	return $output;
}

function random_password($length = 15)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
	$password = substr(str_shuffle($chars), 0, $length) . rand(0,9);
	return $password;
}


function generate_password($string_to_hash, $salt)
{
	$string_to_hash .= $salt;
	$password = hash("sha512", $string_to_hash);
	
	return $password;
}


function test_input($data)
{
	$data = trim($data);
	$data = stripcslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>

<h2>SPM (Simple Password Manager)</h2>


<h3>Storing a password with a site</h3>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

  <div class="container">
  <div>
    <label><b>Site name</b></label>
    <input type="text" name="site_name_store" value="<?php echo $site_name_store;?>" required>
	<span class="error"> <?php echo $site_name_store_error;?></span>
	
  </div>
  <div>  
    <label><b>Password</b></label>
    <input type="password" name="site_password_store" value="" required>
	<span class="error"> <?php echo $site_password_store_error;?></span>	
  </div>      
  </div>   
 <button type="submit" name="store_password-btn" value="store_password">Store password</button>
 <br><br>
 
</form>


<h3>Retrieving a stored password</h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

  <div class="container">
  <div>
    <label><b>Site name</b></label>
    <input type="text" name="site_name_get" value="<?php echo $site_name_get;?>" required>
	<span class="error"> <?php echo $site_name_get_error;?></span>
	<span class="display"> <?php echo $retrieved_password;?></span>
  </div>
 <button type="submit" name="get_password-btn" value="get_password">Get password</button> 
 <br><br>

</form>

<h3>Generate a password</h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<div>
		<button type="submit" name="generate_password-btn" value="generate_password">Generate password</button>
		<div class="display"> <?php echo $generated_password;?></div>
		<br><br>
		<br><br>
	</div>
</form>
 
 
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<div>
		<button type="submit" name="logout-btn" value="logout">Logout</button>
	</div>
</form>

</body>
</html>