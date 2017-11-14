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
// session_start();
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

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(empty($_POST["username"]))
	{
		$username_error = "Username required";
	}
	else
	{
		$username = test_input($_POST["username"]);
		if(!preg_match("/^[a-zA-Z ]*$/", $username))
		{
			$username_error = "Only letter and white spaces allowed";
		}
	}
	
	if(empty($_POST["password"]))
	{
		$password_error = "Password required";
	}
	else
	{
		$password = test_input($_POST["password"]);
		if(!preg_match("/^[a-zA-Z ]*$/", $password))
		{
			$password_error = "Only letter and white spaces allowed";
		}
	}
	
	// login-btn has been clicked
	if(isset($_POST["login-btn"]) && !empty($_POST["username"]) && !empty($_POST["password"]))
	{
		$login_btn = test_input($_POST["login-btn"]);
		// check username and password in database
		$username_exist = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username'");
		$password_exist = mysqli_query($connection, "SELECT password FROM users WHERE password = '$password'");

		if($username_exist && (mysqli_num_rows($username_exist) > 0) && $password_exist && (mysqli_num_rows($password_exist) > 0))
		{
			// username and password exists
			// login user by redirecting to password_storing.php
			
			header("Location: password_storing.php");
		}
		else
		{
			echo "wrong username or password";
			$login_error = "Wrong username or password";
		}		
	}
	// register-btn has been clicked
	if(isset($_POST["register-btn"]) && !empty($_POST["username"]) && !empty($_POST["password"]))
	{
		$register_btn = test_input($_POST["register-btn"]);
		// check if username already exists in database
		$username_exist = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username'");
		
		if($username_exist && mysqli_num_rows($username_exist) > 0)
		{
			$reigster_error = "Username already exist";
			echo "username already exists";
		}
		else
		{
			// insert username and password into database
			echo "inserted into database";
			/*$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
			if($connection->query($sql) === FALSE)
			{
				// error inserting username and password
				echo "Error: " . $sql . "<br>" . $connection->error;
			}*/
		}		
	}

	
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
    <input type="text" name="username" value="" required>
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