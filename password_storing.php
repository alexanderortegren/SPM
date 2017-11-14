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
// session_start();
include_once 'db_connect.php';

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(isset($_POST["logout-btn"]))
	{
		header("Location: login_register.php");
	}
}





?>

<h2>SPM (Simple Password Manager)</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

  <div class="container">
  <div>
    <label><b>Site name</b></label>
    <input type="text" name="name" value="" required>
  </div>
  <div>  
    <label><b>Password</b></label>
    <input type="password" name="password" value="" required>
  </div>      
  </div>   
 <button type="submit" name="generate-btn" value="login">Generate password</button>
 <button type="submit" name="store-btn" value="register">Store password with sitename</button>



</form>
 
 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<div>
		<button type="submit" name="logout-btn" value="logout">Logout</button>
	</div>
 </form>

</body>
</html>