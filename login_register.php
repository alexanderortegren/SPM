<?php
/*
login_register.php

Page for useres to either login to an existing account or 
registering a new account for the Password manager.

Valid username and password needs to be input for 
redirection to password_storing.php else, it will redirect back
to this page telling the user to input valid info.
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
include_once 'db_connect.php';

$username = "";
$password = "";

$username_error	= "";
$password_error	= "";

$login_error = "";
$register_error = "";

$login_btn = "";
$register_btn = "";
$sql = "";
$salt = "";

$uppercase = "";
$lowercase = "";
$number = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(empty($_POST["username"]))
	{
		$username_error = "Username required";
		$login_error = TRUE;
		$register_error = TRUE;
	}
	else
	{
		$login_error = FALSE;
		$register_error = FALSE;
		$username = test_input($_POST["username"]);
		
		$uppercase = preg_match('@[A-Z]@', $username);
		$lowercase = preg_match('@[a-z]@', $username);
		$number    = preg_match('@[0-9]@', $username);
		
		if(!$uppercase || !$lowercase || !$number)
		{
			$username_error = "Username needs at least 1 uppercase, 1 lowercase, and 1 number";
			$login_error = TRUE;
			$register_error = TRUE;
		}
		if(strlen($username) < 8)
		{
			$login_error = TRUE;
			$register_error = TRUE;			
			$username_error = "Username size: 8 char minimum";
		}
		if(strlen($username) > 150)
		{
			$login_error = TRUE;
			$register_error = TRUE;			
			$username_error = "Username size: 150 char maximum";
		}
	}
	
	if(empty($_POST["password"]))
	{
		$password_error = "Password required";
		$login_error = TRUE;
		$register_error = TRUE;
	}
	else
	{
		$login_error = FALSE;
		$register_error = FALSE;
		$password = test_input($_POST["password"]);
		
		$uppercase = preg_match('@[A-Z]@', $password);
		$lowercase = preg_match('@[a-z]@', $password);
		$number    = preg_match('@[0-9]@', $password);
		
		if(!$uppercase || !$lowercase || !$number)
		{
			$password_error = "Password needs at least 1 uppercase, 1 lowercase, and 1 number";
			$login_error = TRUE;
			$register_error = TRUE;
		}
		if(strlen($password) < 8)
		{
			$login_error = TRUE;
			$register_error = TRUE;			
			$password_error = "Password size: 8 char minimum";
		}
		if(strlen($password) > 150)
		{
			$login_error = TRUE;
			$register_error = TRUE;			
			$password_error = "Password size: 150 char maximum";
		}


	}
	
	// login-btn has been clicked
	if($login_error == FALSE && isset($_POST["login-btn"]))// && !empty($_POST["username"]) && !empty($_POST["password"]))
	{
		$login_btn = test_input($_POST["login-btn"]);
		// check username and password in database
		$username_exist = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username'");
		
		$password = generate_password($password, $username);
		$password_exist = mysqli_query($connection, "SELECT password FROM users WHERE password = '$password'");

		if($username_exist && (mysqli_num_rows($username_exist) > 0) && $password_exist && (mysqli_num_rows($password_exist) > 0))
		{
			// username and password exists
			// login user by redirecting to password_storing.php
			$_SESSION['username'] = $username;
			header("Location: password_storing.php");
		}
		else
		{
			echo "wrong username or password";
			$login_error = TRUE;
		}		
	}
	// register-btn has been clicked
	if($register_error == FALSE && isset($_POST["register-btn"]))// && !empty($_POST["username"]) && !empty($_POST["password"]))
	{
		$register_btn = test_input($_POST["register-btn"]);
		// check if username already exists in database
		$username_exist = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username'");
		
		if($username_exist && mysqli_num_rows($username_exist) > 0)
		{
			//echo "username already exists";
			$username_error = "Username already exist";
			$reigster_error = TRUE;
		}
		else
		{
			// insert username and password into database
			// user registered
			echo "Registered. You can now login.";
			
			$password = generate_password($password, $username);
			
			$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
			if($connection->query($sql) === FALSE)
			{
				// error inserting username and password
				echo "Error: " . $sql . "<br>" . $connection->error;
			}
		}		
	}
	/*
	if($login_error == TRUE)
	{
		//increment login_error counter
		$_SESSION['login_counter'] = $_SESSION['login_counter'] + 1;
	}
	
	*/
	
}

function random_password($length = 15)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
	$password = substr(str_shuffle($chars), 0, $length);
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

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

  <div class="container">
  <div>
    <label><b>Username</b></label>
    <input type="text" name="username" value="<?php echo $username;?>" required>
	<span class="error"> <?php echo $username_error;?></span>
	<br><br>
  </div>
  <div>  
    <label><b>Password</b></label>
    <input type="password" name="password" value="" required>
	<span class="error"> <?php echo $password_error;?></span>
	<br><br>
  </div>      
  </div>   
 <button type="submit" name="login-btn" value="login">Login</button>
 <button type="submit" name="register-btn" value="register">Register</button>


</form>

</body>
</html>